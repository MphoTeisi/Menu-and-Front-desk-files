<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEnquiryRequest;
use App\Http\Requests\UpdateEnquiryRequest;
use App\Http\Requests\StoreFollowupRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; // ADDED: For type hints
use App\Models\Enquiry;
use App\Models\EnquiryFollowup;
use Yajra\DataTables\Facades\DataTables;

class EnquiryController extends Controller
{
    /**
     * Display enquiry list or return DataTable JSON.
     */
  public function index(Request $request)
{
    if ($request->ajax()) {

        $query = Enquiry::with('followups')->select([
            'id',
            'name',
            'phone',
            'email',
            'address',
            'description',
            'enquiry_date',
            'next_follow_up_date',
            'last_follow_up_date',
            'status'
        ])->latest();

       return DataTables::of($query)
    ->addColumn('address', function (Enquiry $enquiry) {
        return $enquiry->address ?? '-';
    })
    ->addColumn('description', function (Enquiry $enquiry) {
        return $enquiry->description ?? '-';
    })


    ->addColumn('actions', function (Enquiry $enquiry) {
            return '
                <button class="btn btn-sm btn-success view-btn"
                        data-url="' . route('admin.enquiries.show', $enquiry->id) . '"
                        title="View Details">
                    <i class="fa fa-eye"></i>
                </button>
                <button class="btn btn-sm btn-info followup-btn"
                        data-url="' . route('admin.enquiries.followup', $enquiry->id) . '"
                        title="Follow Up">
                    <i class="fa fa-phone"></i>
                </button>
                <button class="btn btn-sm btn-primary edit-btn"
                        data-url="' . route('admin.enquiries.edit', $enquiry->id) . '"
                        title="Edit">
                    <i class="fa fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger delete-btn"
                        data-url="' . route('admin.enquiries.destroy', $enquiry->id) . '"
                        title="Delete">
                    <i class="fa fa-trash"></i>
                </button>
            ';
        })
    ->editColumn('enquiry_date', fn(Enquiry $enquiry) =>
        $enquiry->enquiry_date ? \Carbon\Carbon::parse($enquiry->enquiry_date)->format('d/m/Y') : '-'
    )
    ->editColumn('next_follow_up_date', fn(Enquiry $enquiry) =>
        $enquiry->next_follow_up_date ? \Carbon\Carbon::parse($enquiry->next_follow_up_date)->format('d/m/Y') : '-'
    )
    ->rawColumns(['actions'])
    ->make(true);

    }
    // ✅ FIX: pass null by default so view doesn't break
    $enquiry = null;
    return view('admin.enquiries.index', compact('enquiry'));
}

    /**
     * Show enquiry details.
     */

    /**
 * Display enquiry details with follow-up history
 */
public function show(Enquiry $enquiry)
{
    // Load enquiry with all follow-ups ordered by date
    $enquiry->load(['followups' => function($query) {
        $query->orderBy('followup_date', 'desc');
    }]);

    return view('admin.enquiries._show', compact('enquiry'));
}

    /**
     * Show the create form (modal partial).
     */
    public function create()
    {
        return view('admin.enquiries._form');
    }

    /**
     * Store a newly created enquiry.
     */
    public function store(StoreEnquiryRequest $request): JsonResponse // ADDED: Return type
    {
        try {
            $enquiry = Enquiry::create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Enquiry created successfully',
                'data' => $enquiry
            ]);
        } catch (\Exception $e) {
            \Log::error('Enquiry creation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create enquiry. Please try again.'
            ], 500);
        }
    }

    /**
     * Show the edit form.
     */
    public function edit(Enquiry $enquiry)
    {
        $enquiry->load('followups'); // ADDED: Load follow-ups for history

        return view('admin.enquiries._form', compact('enquiry'));
    }

    /**
     * Update an existing enquiry.
     */
    public function update(UpdateEnquiryRequest $request, Enquiry $enquiry): JsonResponse // ADDED: Return type
    {
        try {
            $enquiry->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Enquiry updated successfully',
                'data' => $enquiry
            ]);
        } catch (\Exception $e) {
            \Log::error('Enquiry update failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update enquiry. Please try again.'
            ], 500);
        }
    }

    /**
     * Delete an enquiry.
     */
    public function destroy(Enquiry $enquiry): JsonResponse // ADDED: Return type
    {
        try {
            // Delete associated follow-ups first
            $enquiry->followups()->delete(); // ADDED: Clean up follow-ups

            $enquiry->delete();

            return response()->json([
                'success' => true,
                'message' => 'Enquiry deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Enquiry deletion failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete enquiry. Please try again.'
            ], 500);
        }
    }

    /**
 * Show follow-up modal.
 */
        public function followup(Enquiry $enquiry)
        {
            // Load recent follow-ups for context
            $enquiry->load(['followups' => function($query) {
                $query->orderBy('followup_date', 'desc')->take(5);
            }]);

            return view('admin.enquiries._followup', compact('enquiry'));
        }

        /**
         * Get follow-ups data for DataTable
         */
       /**
 * Get follow-ups data for DataTable
 */
        public function followupsData(Request $request)
        {
            if ($request->ajax()) {
                $query = EnquiryFollowup::with('enquiry')
                    ->select(['enquiry_followups.*'])
                    ->latest('followup_date');

                return DataTables::of($query)
                    ->addColumn('enquiry_name', function (EnquiryFollowup $followup) {
                        return $followup->enquiry->name ?? 'N/A';
                    })
                    ->addColumn('phone', function (EnquiryFollowup $followup) {
                        return $followup->enquiry->phone ?? 'N/A';
                    })
                    ->addColumn('next_follow_up_date', function (EnquiryFollowup $followup) {
                        return $followup->enquiry->next_follow_up_date ?? null;
                    })
                    ->addColumn('status', function (EnquiryFollowup $followup) {
                        return $followup->enquiry->status ?? 'active';
                    })
                  ->addColumn('actions', function (EnquiryFollowup $followup) {

                    return '
                            <button class="btn btn-sm btn-primary edit-followup-btn"
                                    data-url="' . route('admin.enquiries.followups.edit', $followup->id) . '"
                                    title="Edit Follow-up">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-followup-btn"
                                    data-url="' . route('admin.enquiries.followups.destroy', $followup->id) . '"
                                    title="Delete Follow-up">
                                <i class="fa fa-trash"></i>
                            </button>
                        ';
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
            }

            return abort(404);
        }
                /**
         * Store follow-up details.
         */
        public function storeFollowup(StoreFollowupRequest $request, Enquiry $enquiry): JsonResponse
        {
            try {
                \DB::beginTransaction();

                // Parse the follow-up date from the form
                $followupDate = \Carbon\Carbon::parse($request->followup_date);

                // Create follow-up record with user-selected date
                $followup = EnquiryFollowup::create([
                    'enquiry_id' => $enquiry->id,
                    'description' => $request->notes,
                    'followup_date' => $followupDate,
                    'user_id' => auth()->id(),
                ]);

                // Prepare update data for enquiry
                $updateData = [];

                // Update last_follow_up_date only if this is the most recent follow-up
                $latestFollowupDate = $enquiry->followups()->max('followup_date');
                if (!$latestFollowupDate || $followupDate->greaterThanOrEqualTo($latestFollowupDate)) {
                    $updateData['last_follow_up_date'] = $followupDate->toDateString();
                }

                // Update next follow-up date if provided
                if ($request->filled('next_follow_up_date')) {
                    $updateData['next_follow_up_date'] = $request->next_follow_up_date;
                } else {
                    // If no next date provided, clear it (follow-up completed)
                    $updateData['next_follow_up_date'] = null;
                }

                // Update status if provided
                if ($request->filled('update_status')) {
                    $updateData['status'] = $request->update_status;
                }

                // Update the enquiry if we have changes
                if (!empty($updateData)) {
                    $enquiry->update($updateData);
                }

                \DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Follow-up saved successfully!'
                ]);

            } catch (\Exception $e) {
                \DB::rollBack();
                \Log::error('Followup creation failed: ' . $e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save follow-up. Please try again.'
                ], 500);
            }
        }
        /**
         * Show edit form for a follow-up
         */
        public function editFollowup(EnquiryFollowup $followup)
        {
            return view('admin.enquiries._edit_followup', compact('followup'));
        }

        /**
         * Update a follow-up
         */
        public function updateFollowup(Request $request, EnquiryFollowup $followup)
        {
            try {
                $validated = $request->validate([
                    'description' => 'required|string|min:5|max:1000',
                    'followup_date' => 'required|date|before_or_equal:now',
                ]);

                $followup->update($validated);

                return response()->json([
                    'success' => true,
                    'message' => 'Follow-up updated successfully!'
                ]);

            } catch (\Exception $e) {
                \Log::error('Followup update failed: ' . $e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update follow-up. Please try again.'
                ], 500);
            }
        }

        /**
         * Delete a follow-up
         */
        public function destroyFollowup(EnquiryFollowup $followup)
        {
            try {
                $followup->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Follow-up deleted successfully!'
                ]);

            } catch (\Exception $e) {
                \Log::error('Followup deletion failed: ' . $e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete follow-up. Please try again.'
                ], 500);
            }
        }


}
