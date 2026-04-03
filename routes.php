<?php

use App\Core\Router;

$router = new Router();

// Public
$router->get('/',                     'HomeController', 'index');
$router->get('/mentions-legales',     'HomeController', 'mentions');

// Auth
$router->get( '/connexion',                'AuthController', 'loginForm');
$router->post('/connexion',                'AuthController', 'login');
$router->get( '/deconnexion',              'AuthController', 'logout');
$router->get( '/inscription',              'AuthController', 'registerForm');
$router->post('/inscription',              'AuthController', 'register');
$router->get( '/inscription/entreprise',   'AuthController', 'registerCompanyForm');
$router->post('/inscription/entreprise',   'AuthController', 'registerCompany');
$router->get( '/inscription/en-attente',   'AuthController', 'pending'); 
$router->post( '/compte/supprimer',         'AuthController', 'deleteAccountForm');
$router->post('/compte/supprimer',         'AuthController', 'deleteAccount');

// Dashboards
$router->get('/dashboard',             'HomeController', 'dashboard');
$router->get('/dashboard/pilote',      'HomeController', 'dashboardPilote');
$router->get('/dashboard/admin',       'HomeController', 'dashboardAdmin');
$router->get('/dashboard/entreprise',  'CompanyController', 'dashboard');

// Profil
$router->get( '/profil', 'HomeController', 'profile');
$router->post('/profil', 'HomeController', 'updateProfile');

// Gestion des users admins et pilotes
$router->get( '/admin/utilisateurs',            'UserController', 'index');
$router->get( '/admin/utilisateurs/creer',      'UserController', 'create');
$router->post('/admin/utilisateurs/creer',      'UserController', 'store');
$router->get( '/admin/utilisateurs/{id}',       'UserController', 'show');
$router->get( '/admin/utilisateurs/{id}/modifier',  'UserController', 'edit');
$router->post('/admin/utilisateurs/{id}/modifier',  'UserController', 'update');
$router->post('/admin/utilisateurs/{id}/supprimer', 'UserController', 'destroy');

// Actions étudiants
$router->post('/admin/utilisateurs/approuver-etudiant', 'UserController', 'approveStudent'); 
$router->post('/admin/utilisateurs/refuser-etudiant',   'UserController', 'rejectStudent'); 

// Admin : Demandes Inscriptions Entreprises
$router->get( '/admin/comptes-entreprises',                'CompanyAccountController', 'adminIndex');
$router->get( '/admin/comptes-entreprises/{id}',           'CompanyAccountController', 'adminShow');
$router->post('/admin/comptes-entreprises/{id}/approuver', 'CompanyAccountController', 'adminApprove');
$router->post('/admin/comptes-entreprises/{id}/refuser',   'CompanyAccountController', 'adminReject');
$router->get( '/admin/documents/{filename}',               'CompanyAccountController', 'viewDocument');

// Admin : Demandes Inscriptions Pilotes
$router->get( '/admin/comptes-pilotes',                    'CompanyAccountController', 'adminPiloteIndex');
$router->post('/admin/validations/pilotes/{id}/approuver', 'CompanyAccountController', 'adminPiloteApprove');
$router->post('/admin/validations/pilotes/{id}/refuser',   'CompanyAccountController', 'adminPiloteReject');

// Admin : Demandes Inscriptions Étudiants
$router->get( '/admin/comptes-etudiants',                  'CompanyAccountController', 'adminStudentIndex');

// Entreprises (Annuaire & Gestion Admin)
$router->get( '/entreprises',                  'CompanyController', 'index');
$router->get( '/entreprises/creer',            'CompanyController', 'create');
$router->post('/entreprises/creer',            'CompanyController', 'store');
$router->get( '/entreprises/{id}',             'CompanyController', 'show');
$router->get( '/entreprises/{id}/modifier',    'CompanyController', 'edit');   
$router->post('/entreprises/{id}/modifier',    'CompanyController', 'update'); 
$router->post('/entreprises/{id}/supprimer',   'CompanyController', 'destroy'); 
$router->post('/entreprises/{id}/evaluer',     'CompanyController', 'review');

// Espace Entreprise (Compte validé uniquement)
$router->get( '/entreprise/offres',            'CompanyController', 'myOffers');
$router->get( '/entreprise/candidatures',      'CompanyController', 'applications');
$router->post('/entreprise/candidatures/{id}/statut', 'CompanyController', 'updateApplicationStatus');
$router->get( '/entreprise/modifier',          'CompanyController', 'editCompany');
$router->post('/entreprise/modifier',          'CompanyController', 'updateCompany');

// Offres
$router->get( '/offres',                'OfferController', 'index');
$router->get( '/offres/creer',          'OfferController', 'create');
$router->post('/offres/creer',          'OfferController', 'store');
$router->get( '/offres/{id}',           'OfferController', 'show');
$router->get( '/offres/{id}/modifier',  'OfferController', 'edit');
$router->post('/offres/{id}/modifier',  'OfferController', 'update');
$router->post('/offres/{id}/supprimer', 'OfferController', 'destroy');

// Candidatures
$router->post('/offres/{id}/postuler',  'ApplicationController', 'store');
$router->get( '/candidatures',          'ApplicationController', 'index');
$router->get( '/uploads/{filename}',    'ApplicationController', 'downloadCv');

// Wishlist & Autres
$router->get( '/wishlist',         'WishlistController', 'index');
$router->post('/wishlist/ajouter', 'WishlistController', 'add');
$router->post('/wishlist/retirer', 'WishlistController', 'remove');
$router->get( '/stats',            'StatsController', 'index');

return $router;
