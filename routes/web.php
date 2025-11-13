<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\Admin\ClubController as AdminClubController;
use App\Http\Controllers\Admin\FeeController as AdminFeeController;
use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\ContextController as AdminContextController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperDashboard;
use App\Http\Controllers\SuperAdmin\UsersController as SuperUsers;
use App\Http\Controllers\SuperAdmin\MembershipsController as SuperMemberships;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/invoices/{id}/pdf', [PdfController::class, 'invoice'])->name('invoices.pdf');
    // Guardian portal
    Route::get('/guardian/children', [\App\Http\Controllers\Guardian\PortalController::class, 'children'])->name('guardian.children');
    Route::get('/guardian/children/create', [\App\Http\Controllers\Guardian\PortalController::class, 'createChild'])->name('guardian.children.create');
    Route::post('/guardian/children', [\App\Http\Controllers\Guardian\PortalController::class, 'storeChild'])->name('guardian.children.store');
    Route::get('/guardian/children/{player}/edit', [\App\Http\Controllers\Guardian\PortalController::class, 'editChild'])->name('guardian.children.edit');
    Route::post('/guardian/children/{player}', [\App\Http\Controllers\Guardian\PortalController::class, 'updateChild'])->name('guardian.children.update');
    Route::get('/guardian/sessions', [\App\Http\Controllers\Guardian\PortalController::class, 'sessions'])->name('guardian.sessions');
    Route::post('/guardian/records/{record}/vote', [\App\Http\Controllers\Guardian\PortalController::class, 'vote'])->name('guardian.vote');
    // Guardian invoices & proof of payment upload
    Route::get('/guardian/invoices', [\App\Http\Controllers\Guardian\InvoiceController::class, 'index'])->name('guardian.invoices.index');
    Route::get('/guardian/invoices/{invoice}', [\App\Http\Controllers\Guardian\InvoiceController::class, 'show'])->name('guardian.invoices.show');
    Route::post('/guardian/invoices/{invoice}/upload-proof', [\App\Http\Controllers\Guardian\InvoiceController::class, 'uploadProof'])->name('guardian.invoices.upload_proof');
    // Web Push subscription endpoints
    Route::post('/push/subscribe', [\App\Http\Controllers\PushController::class, 'subscribe'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [\App\Http\Controllers\PushController::class, 'unsubscribe'])->name('push.unsubscribe');
    // Coach profile & qualifications
    Route::get('/coach/profile', [\App\Http\Controllers\CoachProfileController::class, 'edit'])->name('coach.profile.edit');
    Route::post('/coach/profile', [\App\Http\Controllers\CoachProfileController::class, 'update'])->name('coach.profile.update');
    Route::post('/coach/qualifications', [\App\Http\Controllers\CoachProfileController::class, 'storeQualification'])->name('coach.qualifications.store');
    Route::post('/coach/qualifications/{id}/delete', [\App\Http\Controllers\CoachProfileController::class, 'destroyQualification'])->name('coach.qualifications.destroy');
});

// Web installer (no auth) — avoid cookie/session middlewares to work without APP_KEY
Route::get('/install', [\App\Http\Controllers\InstallerController::class, 'index'])
    ->withoutMiddleware([
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
    ])
    ->name('install.index');

Route::post('/install', [\App\Http\Controllers\InstallerController::class, 'install'])
    ->withoutMiddleware([
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
    ])
    ->name('install.run');

Route::get('/install/success', [\App\Http\Controllers\InstallerController::class, 'success'])
    ->withoutMiddleware([
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    ])
    ->name('install.success');

// Player registration requires auth (guests redirected to login, then back via intended URL)
Route::get('/register/{club}/player', [RegistrationController::class, 'create'])
    ->middleware(['auth'])
    ->name('registration.create');
Route::post('/register/{club}/player', [RegistrationController::class, 'store'])
    ->middleware(['auth'])
    ->name('registration.store');
Route::get('/register/thank-you', [RegistrationController::class, 'thankyou'])->name('registration.thankyou');

Route::middleware(['auth','require_active_club'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('clubs', AdminClubController::class)->only(['index','create','store','edit','update']);
    Route::resource('fees', AdminFeeController::class);
    Route::resource('invoices', AdminInvoiceController::class)->only(['index','create','store','show','destroy']);
    Route::post('invoices/regenerate/player/{player}', [AdminInvoiceController::class, 'regenerateForPlayer'])->name('invoices.regenerate_for_player');
    Route::post('invoices/{invoice}/payments', [AdminPaymentController::class, 'store'])->name('invoices.payments.store');
    // Attendance
    Route::get('attendance', [\App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('attendance/create', [\App\Http\Controllers\Admin\AttendanceController::class, 'create'])->name('attendance.create');
    Route::post('attendance', [\App\Http\Controllers\Admin\AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('attendance/{session}', [\App\Http\Controllers\Admin\AttendanceController::class, 'show'])->name('attendance.show');
    Route::get('attendance/{session}/print', [\App\Http\Controllers\Admin\AttendanceController::class, 'printTeams'])->name('attendance.print');
    Route::get('attendance/{session}/team/{team}/print', [\App\Http\Controllers\Admin\AttendanceController::class, 'printTeam'])->name('attendance.team.print');
    Route::get('attendance/{session}/assign', [\App\Http\Controllers\Admin\AttendanceController::class, 'assign'])->name('attendance.assign');
    Route::post('attendance/{session}/assign', [\App\Http\Controllers\Admin\AttendanceController::class, 'saveAssignments'])->name('attendance.assign.save');
    Route::patch('attendance/{session}/records/{record}/rsvp', [\App\Http\Controllers\Admin\AttendanceController::class, 'updateRsvp'])->name('attendance.rsvp.update');
    Route::patch('attendance/{session}/records/{record}/presence', [\App\Http\Controllers\Admin\AttendanceController::class, 'updatePresence'])->name('attendance.presence.update');
    // Team Managers management (Club Admin/Manager)
    Route::get('team-managers', [\App\Http\Controllers\Admin\TeamManagerController::class, 'index'])->name('team_managers.index');
    Route::get('team-managers/create', [\App\Http\Controllers\Admin\TeamManagerController::class, 'create'])->name('team_managers.create');
    Route::post('team-managers', [\App\Http\Controllers\Admin\TeamManagerController::class, 'store'])->name('team_managers.store');
    Route::get('team-managers/{membership}/edit', [\App\Http\Controllers\Admin\TeamManagerController::class, 'edit'])->name('team_managers.edit');
    Route::patch('team-managers/{membership}', [\App\Http\Controllers\Admin\TeamManagerController::class, 'update'])->name('team_managers.update');
    // Teams
    Route::get('teams', [\App\Http\Controllers\Admin\TeamController::class, 'index'])->name('teams.index');
    Route::get('teams/create', [\App\Http\Controllers\Admin\TeamController::class, 'create'])->name('teams.create');
    Route::post('teams', [\App\Http\Controllers\Admin\TeamController::class, 'store'])->name('teams.store');
    // Player approvals
    Route::get('players', [\App\Http\Controllers\Admin\PlayerController::class, 'index'])->name('players.index');
    Route::post('players/{player}/approve', [\App\Http\Controllers\Admin\PlayerController::class, 'approve'])->name('players.approve');
    Route::post('players/{player}/reject', [\App\Http\Controllers\Admin\PlayerController::class, 'reject'])->name('players.reject');
    Route::post('players/{player}/shirt-handed-out', [\App\Http\Controllers\Admin\PlayerController::class, 'markShirtHandedOut'])->name('players.shirt_handed_out');
    // Notices
    Route::get('notices/create', [\App\Http\Controllers\Admin\NoticeController::class, 'create'])->name('notices.create');
    Route::post('notices', [\App\Http\Controllers\Admin\NoticeController::class, 'store'])->name('notices.store');
    Route::get('notices/{notice}/edit', [\App\Http\Controllers\Admin\NoticeController::class, 'edit'])->name('notices.edit');
    Route::post('notices/{notice}', [\App\Http\Controllers\Admin\NoticeController::class, 'update'])->name('notices.update');
    Route::post('notices/{notice}/delete', [\App\Http\Controllers\Admin\NoticeController::class, 'destroy'])->name('notices.destroy');
    // Confirm guardian proof -> mark invoice paid
    Route::post('invoices/{invoice}/confirm-proof', [\App\Http\Controllers\Admin\InvoiceController::class, 'confirmProof'])->name('invoices.confirm_proof');
    // Coach invitations (Club Manager)
    Route::get('coaches/invitations/create', [\App\Http\Controllers\Admin\CoachInvitationController::class, 'create'])->name('coaches.invitations.create');
    Route::post('coaches/invitations', [\App\Http\Controllers\Admin\CoachInvitationController::class, 'store'])->name('coaches.invitations.store');
    // Team Manager: assign coaches to teams
    Route::get('coaches/assign', [\App\Http\Controllers\Admin\TeamCoachController::class, 'create'])->name('coaches.assign');
    Route::post('coaches/assign', [\App\Http\Controllers\Admin\TeamCoachController::class, 'store'])->name('coaches.assign.store');
    // Coaches directory (Club/Team Manager view)
    Route::get('coaches', [\App\Http\Controllers\Admin\CoachController::class, 'index'])->name('coaches.index');
    Route::get('coaches/{user}', [\App\Http\Controllers\Admin\CoachController::class, 'show'])->name('coaches.show');
});

// Set active club (outside require_active_club)
Route::middleware(['auth'])->post('/admin/active-club', [AdminContextController::class, 'setActiveClub'])->name('admin.active-club.set');

require __DIR__.'/auth.php';

// Public coach onboarding (by token)
Route::get('/coach/invite/{token}', [\App\Http\Controllers\CoachOnboardingController::class, 'show'])->name('coach.invite.accept');
Route::post('/coach/invite/{token}', [\App\Http\Controllers\CoachOnboardingController::class, 'submit'])->name('coach.invite.submit');

// Super Admin area
Route::middleware(['auth','role:org_admin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/', [SuperDashboard::class, 'index'])->name('dashboard');
    Route::get('/users', [SuperUsers::class, 'index'])->name('users.index');
    Route::get('/users/create', [SuperUsers::class, 'create'])->name('users.create');
    Route::post('/users', [SuperUsers::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [SuperUsers::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}', [SuperUsers::class, 'update'])->name('users.update');

    Route::get('/memberships', [SuperMemberships::class, 'index'])->name('memberships.index');
    Route::post('/memberships', [SuperMemberships::class, 'store'])->name('memberships.store');
    // Impersonation
    Route::post('/impersonate/{club}', [\App\Http\Controllers\SuperAdmin\ImpersonationController::class, 'start'])->name('impersonate.start');
    Route::post('/impersonate/stop', [\App\Http\Controllers\SuperAdmin\ImpersonationController::class, 'stop'])->name('impersonate.stop');
});
