<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\News;
use App\Models\Page;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', ['new' => Inquiry::whereStatus('new')->count(), 'projects' => Project::published()->count(), 'news' => News::published()->count(), 'drafts' => Page::whereStatus('draft')->get()]);
    }

    public function inquiries(Request $r)
    {
        $q = Inquiry::with('sector')->latest();
        if (in_array($r->query('status'), ['new', 'following', 'closed'])) {
            $q->where('status', $r->query('status'));
        }

return view('admin.inquiries', ['inquiries' => $q->paginate(20)->withQueryString()]);
    }

    public function updateInquiry(Request $r, Inquiry $inquiry)
    {
        $inquiry->update($r->validate(['status' => ['required', Rule::in(['new', 'following', 'closed'])], 'notes' => 'nullable|string|max:6000']));

        return back()->with('success', 'تم تحديث الطلب.');
    }
}
