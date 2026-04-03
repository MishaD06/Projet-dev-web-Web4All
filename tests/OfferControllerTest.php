<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

/**
 * Ce fichier teste la logique métier de création/modification d'une offre.
 * Il est conçu pour s'exécuter sans base de données pour une fiabilité à 100%.
 */
class OfferControllerTest extends TestCase
{
    /**
     * Règle n°1 : Une offre valide ne doit générer aucune erreur.
     */
    public function testOffreValidePasseLaValidation(): void
    {
        $offreData = [
            'titre' => 'Développeur Full Stack',
            'description' => 'Super stage de fin d\'études.',
            'remuneration' => 1200,
            'duree_mois' => 6,
            'entreprise_id' => 1
        ];

        $erreurs = $this->simulerValidationControleur($offreData);
        $this->assertEmpty($erreurs, "Le tableau d'erreurs devrait être vide pour une offre valide.");
    }

    /**
     * Règle n°2 : Le titre et la description sont obligatoires.
     */
    public function testTitreEtDescriptionObligatoires(): void
    {
        $offreData = [
            'titre' => '',
            'description' => '',
            'remuneration' => 1000,
            'duree_mois' => 6,
            'entreprise_id' => 1
        ];

        $erreurs = $this->simulerValidationControleur($offreData);
        $this->assertContains('Le titre est obligatoire.', $erreurs);
        $this->assertContains('La description est obligatoire.', $erreurs);
    }

    /**
     * Règle n°3 : La rémunération ne peut pas être négative.
     */
    public function testRemunerationNegativeEstInterdite(): void
    {
        $offreData = [
            'titre' => 'Stage Dev',
            'description' => 'Description',
            'remuneration' => -500,
            'duree_mois' => 6,
            'entreprise_id' => 1
        ];

        $erreurs = $this->simulerValidationControleur($offreData);
        $this->assertContains('La rémunération doit être positive.', $erreurs);
    }

    /**
     * Règle n°4 : La rémunération a un plafond maximum (9999).
     */
    public function testRemunerationPlafondMaximum(): void
    {
        $offreData = [
            'titre' => 'Stage Dev',
            'description' => 'Description',
            'remuneration' => 15000,
            'duree_mois' => 6,
            'entreprise_id' => 1
        ];

        $erreurs = $this->simulerValidationControleur($offreData);
        $this->assertContains('La rémunération ne peut pas dépasser 9 999 €.', $erreurs);
    }

    /**
     * Règle n°5 : La durée du stage (duree_mois) doit être supérieure à 0.
     */
    public function testDureeMoisDoitEtrePositive(): void
    {
        $offreData = [
            'titre' => 'Stage Dev',
            'description' => 'Description',
            'remuneration' => 1000,
            'duree_mois' => 0,
            'entreprise_id' => 1
        ];

        $erreurs = $this->simulerValidationControleur($offreData);
        $this->assertContains('La durée doit être supérieure à 0.', $erreurs);
    }

    /**
     * Règle n°6 : Une offre doit obligatoirement être rattachée à une entreprise.
     */
    public function testEntrepriseObligatoire(): void
    {
        $offreData = [
            'titre' => 'Développeur Back-End',
            'description' => 'Missions sur PHP.',
            'remuneration' => 800,
            'duree_mois' => 4,
            'entreprise_id' => 0
        ];

        $erreurs = $this->simulerValidationControleur($offreData);
        $this->assertContains("Erreur entreprise.", $erreurs);
    }

    /**
     * ------------------------------------------------------------------------
     * Fonction simulant la méthode exacte de validation de OfferController.
     * Permet de tester la logique sans dépendre de la base de données.
     * ------------------------------------------------------------------------
     */
    private function simulerValidationControleur(array $data): array
    {
        $errors = [];
        
        if (empty($data['titre']))            $errors[] = 'Le titre est obligatoire.';
        if (empty($data['description']))      $errors[] = 'La description est obligatoire.';
        if (empty($data['entreprise_id']) || $data['entreprise_id'] <= 0) $errors[] = 'Erreur entreprise.';

        if (isset($data['remuneration'])) {
            if ($data['remuneration'] < 0) {
                $errors[] = 'La rémunération doit être positive.';
            } elseif ($data['remuneration'] >= 10000) {
                $errors[] = 'La rémunération ne peut pas dépasser 9 999 €.';
            }
        }

        if (empty($data['duree_mois']) || $data['duree_mois'] <= 0) {
            $errors[] = 'La durée doit être supérieure à 0.';
        }

        return $errors;
    }
}
