<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Client;
use App\Models\Invitation;
use App\Models\Manager;
use App\Models\Order\Order;
use App\Models\Video\Video;
use App\Utils\GoogleDriveFileUtils;
use App\Services\Admin\AdminService;
use App\Services\Video\VideoService;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Manager\ManagerRepositoryInterface;
use Exception;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (Manager::where('user_id', $user->id)->exists()) {
            return redirect()->route('dashboard.admin');
        } else if (Client::where('user_id', $user->id)->exists()) {
            return redirect()->route('dashboard.client');
        }

        return Inertia::render('Dashboard');
    }

    public function gDriveToken()
    {
        $client = new \Google_Client();
        $client->setApplicationName('splashr drives');
        $client->setAuthConfig(GoogleDriveFileUtils::getOAuthCredentialsFile());
        $client->setRedirectUri(env('APP_URL'));
        $client->addScope("https://www.googleapis.com/auth/drive");
        $client->addScope("https://www.googleapis.com/auth/drive.file");
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        $token = null;

        if ($client->isAccessTokenExpired()) {
            $token = $client->fetchAccessTokenWithAssertion();
        } else {
            $token = $client->getAccessToken();
        }

        return response()->json([
            'token' => $token
        ]);
    }

    public function admin()
    {
        return redirect()->route('admin.orders');
    }

    public function client()
    {
        return redirect()->route('client.videos.index');
    }

    public function adminOrders(ManagerRepositoryInterface $managerInterface)
    {
        $adminService = new AdminService();

        $managerUserId = $adminService->getPermissionFilterByRole(Auth::id());

        return Inertia::render('DashboardAdmin', [
            'currentSideNavRoute' => 'admin.orders',
            'currentSideNavName' => 'Orders',
            'orders' => $adminService->getOrdersList($managerUserId, false, false, false, true),
            'manager' => $managerInterface->getAuthenticatedManager()
        ]);
    }

    public function adminClients(ManagerRepositoryInterface $managerInterface)
    {
        $adminService = new AdminService();

        $managerUserId = $adminService->getPermissionFilterByRole(Auth::id());

        return Inertia::render('Admin/Clients/Clients', [
            'currentSideNavRoute' => 'admin.clients',
            'currentSideNavName' => 'Clients',
            'companiesData' => $adminService->getCompaniesList($managerUserId),
            'manager' => $managerInterface->getAuthenticatedManager()
        ]);
    }

    public function showClientProfile(ManagerRepositoryInterface $managerInterface, $companyId)
    {
        $adminService = new AdminService();

        $companyData = $adminService->getCompanyDetailsWithUsers($companyId);

        $managerUserId = $adminService->getPermissionFilterByRole(Auth::id());

        return Inertia::render('Admin/Clients/ClientProfile', [
            'currentSideNavRoute' => 'admin.clients',
            'currentSideNavName' => 'Client Manager',
            'company' => $companyData['data'],
            'manager' => $managerInterface->getAuthenticatedManager(),
            'orders' => [
                'inProgress' => $adminService->getOrdersList($managerUserId, $companyId, true, false),
                'completed' => $adminService->getOrdersList($managerUserId, $companyId, true, true)
            ],
            'data' => [
                'folders' => $companyData['folders']
            ]
        ]);
    }

    public function showOrder(ManagerRepositoryInterface $managerInterface, Order $order)
    {
        $video = Video::findOrFail($order->video_id);

        $videoService = new VideoService();

        $folders = $videoService->getFoldersData($video);

        return Inertia::render('Admin/Orders/OrderDetails', [
            'currentSideNavRoute' => 'admin.orders',
            'currentSideNavName' => 'Order Manager',
            'orderDetails' => (new AdminService())->getOrderDetailsById($order->id, true),
            'folders' => $folders,
            'isSideNavVisible' => 'false',
            'manager' => $managerInterface->getAuthenticatedManager()
        ]);
    }

    public function inviteNewUser($token)
    {
        // verify the token with invitations table
        try {
            $invitation = Invitation::find($token);

            if (!$invitation) {
                return 'This link is not valid anymore';
            }

            // direct to register page
            return view('auth.register-invite', [
                'token' => $invitation->id,
                'invitee_email' => $invitation->invitee_email,
                'metadata' => $invitation->metadata,
            ]);
        } catch (Exception $e) {
            return 'This link is not valid anymore';
        }
    }

    public function adminSettings(ManagerRepositoryInterface $managerInterface)
    {
        return Inertia::render('Admin/Settings/Settings', [
            'currentSideNavRoute' => 'admin.settings',
            'currentSideNavName' => 'Admin Console',
            'manager' => $managerInterface->getAuthenticatedManager()
        ]);
    }

    public function adminSettingsUsers(ManagerRepositoryInterface $managerInterface)
    {
        $managers = Manager::with([
            'user' => function ($queryUser) {
                $queryUser->select('id', 'first_name', 'last_name', 'email', 'profile_photo_path');
            }
        ])
            ->select('id', 'user_id', 'role', 'created_at')
            ->orderByDesc('updated_at')
            ->get();

        // get the pending invitations 
        $invitations = Invitation::where('inviter_id', Auth::id())
            ->orderByDesc('updated_at')
            ->get();

        // merge them together
        $managers = array_merge(
            $invitations->toArray(),
            $managers->toArray()
        );

        return Inertia::render('Admin/Settings/Users', [
            'currentSideNavRoute' => 'admin.settings',
            'currentSideNavName' => 'Users',
            'managers' => $managers,
            'manager' => $managerInterface->getAuthenticatedManager()
        ]);
    }

    public function adminSettingsTeams(ManagerRepositoryInterface $managerInterface)
    {
        return Inertia::render('Admin/Settings/Teams', [
            'currentSideNavRoute' => 'admin.settings',
            'currentSideNavName' => 'Teams',
            'manager' => $managerInterface->getAuthenticatedManager()
        ]);
    }

    public function adminSettingsInvoices(ManagerRepositoryInterface $managerInterface)
    {
        return Inertia::render('Admin/Settings/Invoices', [
            'currentSideNavRoute' => 'admin.settings',
            'currentSideNavName' => 'Invoices',
            'invoices' => (new AdminService())->getInvoicesList(),
            'manager' => $managerInterface->getAuthenticatedManager()
        ]);
    }
}
