<?php

namespace App\Services\Admin;

use App\Models\Credit;
use App\Models\Company;
use App\Models\Manager;
use App\Utils\UserUtils;
use App\Models\Order\Order;
use App\Models\CompanyCredit;
use App\Models\Order\Comment;
use App\Utils\VideoItemUtils;
use App\Models\Order\Activity;
use App\Utils\VideoUtils;
use Illuminate\Support\Facades\DB;

class AdminService
{
    public function getCompaniesList($managerId = false)
    {
        return Company::with(
            [
                'users' => function ($queryUsers) {
                    $queryUsers->select(
                        'users.id as id',
                        'first_name',
                        'last_name',
                        'company_id',
                        'email',
                        'profile_photo_path'
                    );
                },
                'managers' => function ($queryManagers) {
                    $queryManagers->with('user:id,first_name,last_name,email,profile_photo_path')
                        ->select('managers.id as id', 'managers.user_id as user_id');
                },
                'commitment'
            ]
        )
            ->whereHas('users', function ($queryUser) {
                $queryUser->whereNotExists(function ($queryUserInner) {
                    $queryUserInner->select(DB::raw(1))
                        ->from('managers')
                        ->whereRaw('managers.user_id = users.id');
                });
            })
            ->when($managerId, function ($query) use ($managerId) {
                $query->whereHas('managers', function ($queryManager) use ($managerId) {
                    $queryManager->where('managers.user_id', $managerId);
                });
            })
            ->select(
                'companies.id as id',
                'companies.name as name',
                'total_orders_count',
                'active_orders_count'
            )
            ->orderByDesc('companies.updated_at')
            ->get();
    }

    public function searchInCompaniesList($managerId, string $searchText)
    {
        return Company::with(
            [
                'users' => function ($queryUsers) {
                    $queryUsers->select(
                        'users.id as id',
                        'first_name',
                        'last_name',
                        'company_id',
                        'email',
                        'profile_photo_path'
                    );
                },
                'managers' => function ($queryManagers) {
                    $queryManagers->with('user:id,first_name,last_name,email,profile_photo_path')
                        ->select('managers.id as id', 'managers.user_id as user_id');
                }
            ]
        )
            ->whereHas('users', function ($queryUser) {
                $queryUser->whereNotExists(function ($queryUserInner) {
                    $queryUserInner->select(DB::raw(1))
                        ->from('managers')
                        ->whereRaw('managers.user_id = users.id');
                });
            })
            ->when($managerId, function ($query) use ($managerId) {
                $query->whereHas('managers', function ($queryManager) use ($managerId) {
                    $queryManager->where('managers.user_id', $managerId);
                });
            })
            ->where('name', 'ilike', "%{$searchText}%")
            // ->orWhere(function ($query) use ($searchText) {
            //     $query->whereHas('users', function ($queryUsers) use ($searchText) {
            //         $queryUsers->whereNotExists(function ($queryUserInner) {
            //             $queryUserInner->select(DB::raw(1))
            //                 ->from('managers')
            //                 ->whereRaw('managers.user_id = users.id');
            //         })
            //             ->where('first_name', 'ilike', "%{$searchText}%")
            //             ->orWhere('last_name', 'ilike', "%{$searchText}%")
            //             ->orWhere('email', 'ilike', "%{$searchText}%");
            //     });
            // })
            ->select(
                'companies.id as id',
                'companies.name as name',
                'total_orders_count',
                'active_orders_count'
            )
            ->orderByDesc('companies.updated_at')
            ->get();
    }

    public function getOrdersList(
        $managerId = false,
        $companyId = false,
        $isFiltered = false,
        $isCompleted = false,
        $isCommentIncluded = false
    ) {
        return Order::when($isFiltered, function ($query) use ($isCompleted) {
            $query->where('is_completed', $isCompleted);
        })
            ->with([
                'video' => function ($queryForVideo) {
                    $queryForVideo->with('company:id,name')
                        ->select('videos.id as id', 'company_id', 'name', 'status', 'pending_side', 'session_type');
                },
                'managers' => function ($queryForManagers) {
                    $queryForManagers->with('user:id,first_name,last_name,email,profile_photo_path')
                        ->select('managers.id as id', 'user_id');
                }
            ])
            ->when($managerId, function ($query) use ($managerId) {
                $query->whereHas('managers', function ($queryManager) use ($managerId) {
                    $queryManager->where('managers.user_id', $managerId);
                });
            })
            ->when($companyId, function ($query) use ($companyId) {
                $query->whereHas('video', function ($queryVideo) use ($companyId) {
                    $queryVideo->where('videos.company_id', $companyId);
                });
            })
            ->when($isCommentIncluded, function ($query) {
                $query->addSelect([
                    'recent_commenters' => Comment::select('seen_by')
                        ->whereColumn('order_id', 'orders.id')
                        ->orderByDesc('updated_at')
                        ->limit(1)
                ]);
            })
            ->orderByDesc('updated_at')->get();
    }

    public function getOrderDetailsById($orderId, $isCommentIncluded = false)
    {
        $orderDetails = Order::where('id', $orderId)
            ->with(
                [
                    'video' => function ($queryForVideo) {
                        $queryForVideo->with('client.user:id,first_name,last_name,email,profile_photo_path');
                        $queryForVideo->with('formats:id,name,ratio,viewport,icon');
                        $queryForVideo->with('platforms:id,name');
                        $queryForVideo->with('purposes:id,name');
                        $queryForVideo->with('company:id,name,g_media_folder_id');
                    },
                    'creativeDocuments',
                    'managers' => function ($queryForManagers) {
                        $queryForManagers->with('user:id,first_name,last_name,email,profile_photo_path')
                            ->select('managers.id as id', 'user_id');
                    }
                ]
            )
            ->withCount('richContents')
            ->when($isCommentIncluded, function ($query) {
                $query->addSelect([
                    'recent_commenters' => Comment::select('seen_by')
                        ->whereColumn('order_id', 'orders.id')
                        ->orderByDesc('updated_at')
                        ->limit(1)
                ]);
            })
            ->first();

        $orderDetails->managers->each(function ($manager) {
            $manager->makeHidden('pivot');
        });

        return $orderDetails;
    }

    public function searchInOrdersList($managerId, string $searchText)
    {
        return Order::with([
            'managers' => function ($queryForManagers) {
                $queryForManagers->with('user:id,first_name,last_name,email,profile_photo_path')
                    ->select('managers.id as id', 'user_id');
            },
            'video' => function ($queryForVideo) {
                $queryForVideo->with('company:id,name')
                    ->select('videos.id as id', 'company_id', 'name', 'status', 'pending_side');
            }
        ])
            ->when($managerId, function ($query) use ($managerId) {
                $query->whereHas('managers', function ($queryManager) use ($managerId) {
                    $queryManager->where('managers.user_id', $managerId);
                });
            })
            ->addSelect([
                'recent_commenters' => Comment::select('seen_by')
                    ->whereColumn('order_id', 'orders.id')
                    ->orderByDesc('updated_at')
                    ->limit(1)
            ])
            ->where(function ($query) use ($searchText) {
                $query->whereHas('video', function ($queryVideo) use ($searchText) {
                    $queryVideo->where('name', 'ilike', "%{$searchText}%");
                })->orWhereHas('video.company', function ($queryCompany) use ($searchText) {
                    $queryCompany->where('name', 'ilike', "%{$searchText}%");
                });
            })->orderByDesc('updated_at')->get();
    }

    public function searchManagers(string $searchText)
    {
        return Manager::with('user:id,first_name,last_name,email,profile_photo_path')
            ->where(function ($query) use ($searchText) {
                $query->whereHas('user', function ($q) use ($searchText) {
                    $q->where('first_name', 'ilike', "%{$searchText}%")
                        ->orWhere('last_name', 'ilike', "%{$searchText}%")
                        ->orWhere('email', 'ilike', "%{$searchText}%");
                });
            })->select('id', 'user_id')->get();
    }

    public function getCompanyDetailsWithUsers($companyId)
    {
        $data = Company::where('id', $companyId)->with(
            [
                'users' => function ($queryUsers) {
                    $queryUsers->select(
                        'users.id as id',
                        'first_name',
                        'last_name',
                        'company_id',
                        'email',
                        'profile_photo_path'
                    )->orderBy('users.created_at');
                },
                'folders' => function ($queryFolders) {
                    $queryFolders->select('folders.id as id', 'folders.name as name', 'folderable_id');
                },
                'managers' => function ($queryManagers) {
                    $queryManagers->with('user:id,first_name,last_name,email,profile_photo_path')
                        ->select('managers.id as id', 'managers.user_id as user_id');
                },
                'commitment'
            ]
        )
            ->select(
                'companies.id as id',
                'companies.name as name',
                'total_orders_count',
                'active_orders_count',
                'g_media_folder_id'
            )
            ->orderByDesc('companies.updated_at')
            ->first();

        $data->managers->each(function ($manager) {
            $manager->makeHidden('pivot');
        });

        return [
            'data' => [
                'id' => $data->id,
                'name' => $data->name,
                'total_orders_count' => $data->total_orders_count,
                'active_orders_count' => $data->active_orders_count,
                'g_media_folder_id' => $data->g_media_folder_id,
                'users' => $data->users,
                'managers' => $data->managers,
                'commitment' => $data->commitment
            ],
            'folders' => $data->folders->toArray()
        ];
    }

    public function getCreditHistories($companyId)
    {
        return CompanyCredit::where('company_id', $companyId)
            ->with([
                'credit:id,name',
                'manager' => function ($queryManager) {
                    $queryManager->with('user', function ($queryUser) {
                        $queryUser->select(
                            'users.id as id',
                            'first_name',
                            'last_name',
                            'email',
                            'profile_photo_path'
                        );
                    })->select(
                        'managers.id as id',
                        'managers.user_id as user_id'
                    );
                }
            ])
            ->select(
                'company_credit.id as id',
                'credit_id',
                'manager_id',
                'reference_number',
                'quantity',
                'updated_at'
            )
            ->orderByDesc('updated_at')
            ->get();
    }

    public function getCreditTypes()
    {
        return Credit::select('id', 'name')->orderBy('type')->get();
    }

    public function getPermissionFilterByRole($userId)
    {
        $manager = Manager::where('user_id', $userId)->first();
        $managerUserId = $manager->user_id;
        if ($manager->role == UserUtils::ADMIN_SUPER) {
            $managerUserId = false;
        }
        return $managerUserId;
    }

    public function getActivitiesQueryByUser(string $managerId, $userId = false)
    {
        return Activity::with([
            'order' => function ($queryOrder) {
                $queryOrder->with('video:id,name')
                    ->select('orders.id as id', 'video_id');
            },
            'user' => function ($queryUser) {
                $queryUser->select('users.id as id', 'first_name', 'last_name', 'profile_photo_path');
            }
        ])
            ->when($userId, function ($query) use ($userId) {
                $query->whereHas('user', function ($queryUser) use ($userId) {
                    $queryUser->where('users.id', $userId);
                });
            })
            ->when($managerId, function ($query) use ($managerId) {
                $query->whereExists(function ($queryInner) use ($managerId) {
                    $queryInner->select(DB::raw(1))
                        ->from('manager_order')
                        ->whereRaw('manager_order.order_id = activities.order_id')
                        ->where('manager_order.manager_id', '=', $managerId);
                });
            })
            ->orderByDesc('updated_at');
    }

    public function getActivitiesQueryByOrderAndUser(string $managerId, string $orderId)
    {
        return Activity::with([
            'order' => function ($queryOrder) {
                $queryOrder->with('video:id,name')
                    ->select('orders.id as id', 'video_id');
            },
            'user' => function ($queryUser) {
                $queryUser->select('users.id as id', 'first_name', 'last_name', 'profile_photo_path');
            }
        ])
            ->when($managerId, function ($query) use ($managerId) {
                $query->whereExists(function ($queryInner) use ($managerId) {
                    $queryInner->select(DB::raw(1))
                        ->from('manager_order')
                        ->whereRaw('manager_order.order_id = activities.order_id')
                        ->where('manager_order.manager_id', '=', $managerId);
                });
            })
            ->where('order_id', $orderId)
            ->orderByDesc('updated_at');
    }

    public function getInvoicesList($month = false)
    {
        $companiesData = Company::with([
            'videoItems' => function ($queryVideoItems) use ($month) {
                $queryVideoItems
                    ->with('video:id,session_type')
                    ->where('video_items.status', '=', VideoItemUtils::STATUS_APPROVED)
                    ->when($month, function ($queryVideoItemForMonth) use ($month) {
                        $queryVideoItemForMonth->whereMonth('paid_on', '=', $month);
                    });
            },
            'commitment'
        ])
            ->whereHas('commitment')
            ->whereHas('videoItems', function ($queryCompany) use ($month) {
                $queryCompany->where('video_items.status', '=', VideoItemUtils::STATUS_APPROVED)
                    ->when($month, function ($queryVideoItemForMonth) use ($month) {
                        $queryVideoItemForMonth->whereMonth('paid_on', '=', $month);
                    });
            })
            ->select('id', 'name')
            ->get();

        $invoicesData = [];

        foreach ($companiesData as $companyData) {
            $commitment = $companyData->commitment;

            $totalDue = 0;

            foreach ($companyData->videoItems as $videoItem) {
                switch ($videoItem->type) {
                    case VideoItemUtils::TYPE_MASTER:
                        if ($videoItem->video->session_type == VideoUtils::SESSION_MOTION) {
                            $totalDue += $commitment->motion_price_master;
                        } else if ($videoItem->video->session_type == VideoUtils::SESSION_ACTING) {
                            $totalDue += $commitment->acting_price_master;
                        }
                        break;

                    case VideoItemUtils::TYPE_ITERATION:
                        if ($videoItem->video->session_type == VideoUtils::SESSION_MOTION) {
                            $totalDue += $commitment->motion_price_iteration;
                        } else if ($videoItem->video->session_type == VideoUtils::SESSION_ACTING) {
                            $totalDue += $commitment->acting_price_iteration;
                        }
                        break;

                    case VideoItemUtils::TYPE_RICH_CONTENT:
                        if ($videoItem->video->session_type == VideoUtils::SESSION_MOTION) {
                            $totalDue += $commitment->motion_price_rich_content;
                        } else if ($videoItem->video->session_type == VideoUtils::SESSION_ACTING) {
                            $totalDue += $commitment->acting_price_rich_content;
                        }
                        break;
                }
            }

            array_push($invoicesData, [
                'id' => $companyData->id,
                'name' => $companyData->name,
                'quantity' => count($companyData->videoItems),
                'total_due' => $totalDue
            ]);
        }

        return $invoicesData;
    }
}
