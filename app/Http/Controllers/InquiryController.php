<?php

namespace App\Http\Controllers;

use App\Http\Requests\InquiryRequest;
use App\Models\Inquiry;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InquiryController extends Controller
{
    public function store(InquiryRequest $request, string $locale)
    {
        $data = $request->safe()->except('website');
        $data['locale'] = $locale;
        $data['reference'] = 'MD-'.strtoupper(Str::random(10));
        $inquiry = Inquiry::create($data);
        try {
            if (config('mail.inquiry_to')) {
                Mail::raw('New inquiry '.$inquiry->reference.' received. Review securely at '.config('app.url').'/admin/inquiries', fn ($m) => $m->to(config('mail.inquiry_to'))->subject('New website inquiry: '.$inquiry->reference));
                $inquiry->update(['notification_status' => 'sent']);
            } else {
                $inquiry->update(['notification_status' => 'not_configured']);
            }
        } catch (\Throwable $e) {
            $inquiry->update(['notification_status' => 'failed']);
            Log::warning('Inquiry notification failed', ['inquiry_id' => $inquiry->id]);
        }

        return back()->with('success', __('site.received').' '.$inquiry->reference);
    }
}
