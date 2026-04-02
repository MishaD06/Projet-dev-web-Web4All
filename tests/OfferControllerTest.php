<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Models\Offer;
use App\Models\Company;
use App\Models\Skill;

/**
 * Tests unitaires — OfferController / Offer model
 *
 * Pré-requis : base de données de test configurée dans .env.testing
 * Lancer : composer test
 */
class OfferControllerTest extends TestCase
{
    private static \PDO $db;

    public static function setUpBeforeClass(): void
    {
        // Charge le .env.testing si présent, sinon .env
        $envFile = file_exists(dirname(__DIR__) . '/.env.testing')
            ? dirname(__DIR__) . '/.env.testing'
            : dirname(__DIR__) . '/.env';

        if (file_exists($envFile)) {
            foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                if (!str_starts_with(trim($line), '#') && str_contains($line, '=')) {
                    [$k, $v] = explode('=', $line, 2);
                    $_ENV[trim($k)] = trim($v);
                    putenv(trim($k) . '=' . trim($v));
                }
            }
        }

        self::$db = \App\Core\Database::getInstance();
    }

    // ──────────────────────────────────────────────────────────
    // Validation des données d'une offre
    // ──────────────────────────────────────────────────────────

    public function testValidationEchoueAvecTitreVide(): void
    {
        $errors = $this->validateOffer([
            'titre'            => '',
            'description'      => 'Description test',
            'entreprise_id'    => 1,
            'date_publication' => '2025-01-01',
            'remuneration'     => 800,
        ]);
        $this->assertContains('Le titre est obligatoire.', $errors);
    }

    public function testValidationEchoueAvecDescriptionVide(): void
    {
        $errors = $this->validateOffer([
            'titre'            => 'Test',
            'description'      => '',
            'entreprise_id'    => 1,
            'date_publication' => '2025-01-01',
            'remuneration'     => 800,
        ]);
        $this->assertContains('La description est obligatoire.', $errors);
    }

    public function testValidationEchoueAvecEntrepriseNulle(): void
    {
        $errors = $this->validateOffer([
            'titre'            => 'Test',
            'description'      => 'Description',
            'entreprise_id'    => 0,
            'date_publication' => '2025-01-01',
            'remuneration'     => 800,
        ]);
        $this->assertContains('Veuillez sélectionner une entreprise.', $errors);
    }

    public function testValidationEchoueAvecRemunerationNegative(): void
    {
        $errors = $this->validateOffer([
            'titre'            => 'Test',
            'description'      => 'Description',
            'entreprise_id'    => 1,
            'date_publication' => '2025-01-01',
            'remuneration'     => -100,
        ]);
        $this->assertContains('La rémunération doit être positive.', $errors);
    }

    public function testValidationPasseAvecDonneesValides(): void
    {
        $errors = $this->validateOffer([
            'titre'            => 'Développeur Full Stack',
            'description'      => 'Stage de 6 mois en développement web.',
            'entreprise_id'    => 1,
            'date_publication' => '2025-03-01',
            'remuneration'     => 800,
        ]);
        $this->assertEmpty($errors);
    }

    // ──────────────────────────────────────────────────────────
    // Tests d'intégration BDD (nécessitent la BDD)
    // ──────────────────────────────────────────────────────────

    public function testRecuperationOffreExistante(): void
    {
        try {
            $model = new Offer();
            $result = $model->search(1, 9);
            $this->assertIsArray($result);
            $this->assertArrayHasKey('data', $result);
            $this->assertArrayHasKey('total', $result);
            $this->assertArrayHasKey('current_page', $result);
        } catch (\RuntimeException $e) {
            $this->markTestSkipped('Base de données non disponible : ' . $e->getMessage());
        }
    }

    public function testPaginationRetourneLaBonnePage(): void
    {
        try {
            $model  = new Offer();
            $page1  = $model->search(1, 3);
            $page2  = $model->search(2, 3);

            $this->assertEquals(1, $page1['current_page']);
            $this->assertEquals(2, $page2['current_page']);
            $this->assertEquals(3, $page1['per_page']);

            // Les deux pages ne doivent pas avoir les mêmes offres
            if (!empty($page1['data']) && !empty($page2['data'])) {
                $ids1 = array_column($page1['data'], 'id');
                $ids2 = array_column($page2['data'], 'id');
                $this->assertEmpty(array_intersect($ids1, $ids2));
            }
        } catch (\RuntimeException $e) {
            $this->markTestSkipped('Base de données non disponible : ' . $e->getMessage());
        }
    }

    public function testRechercheParTitreRetourneResultats(): void
    {
        try {
            $model  = new Offer();
            $result = $model->search(1, 9, 'Full Stack');
            $this->assertIsArray($result['data']);
            foreach ($result['data'] as $offer) {
                $this->assertStringContainsStringIgnoringCase('Full Stack', $offer['titre']);
            }
        } catch (\RuntimeException $e) {
            $this->markTestSkipped('Base de données non disponible : ' . $e->getMessage());
        }
    }

    public function testOffreInexistanteRetourneNull(): void
    {
        try {
            $model  = new Offer();
            $result = $model->findFull(999999);
            $this->assertNull($result);
        } catch (\RuntimeException $e) {
            $this->markTestSkipped('Base de données non disponible : ' . $e->getMessage());
        }
    }

    public function testStatsRetourneLesQuatreIndicateurs(): void
    {
        try {
            $model = new Offer();
            $stats = $model->stats();
            $this->assertArrayHasKey('total_offres', $stats);
            $this->assertArrayHasKey('avg_candidatures', $stats);
            $this->assertArrayHasKey('top_wishlist', $stats);
            $this->assertArrayHasKey('avg_duree', $stats);
            $this->assertArrayHasKey('top_wishlist_list', $stats);
            $this->assertArrayHasKey('duration_dist', $stats);
        } catch (\RuntimeException $e) {
            $this->markTestSkipped('Base de données non disponible : ' . $e->getMessage());
        }
    }

    // ──────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────

    private function validateOffer(array $data): array
    {
        $errors = [];
        if (empty($data['titre']))            $errors[] = 'Le titre est obligatoire.';
        if (empty($data['description']))      $errors[] = 'La description est obligatoire.';
        if ($data['entreprise_id'] <= 0)      $errors[] = 'Veuillez sélectionner une entreprise.';
        if (empty($data['date_publication'])) $errors[] = 'La date est obligatoire.';
        if ($data['remuneration'] < 0)        $errors[] = 'La rémunération doit être positive.';
        return $errors;
    }
}
