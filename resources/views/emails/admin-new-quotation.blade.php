<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Quotation Request</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; background: #f3f4f6; margin: 0; padding: 24px; color: #1f2937; }
        .wrap { max-width: 680px; margin: 0 auto; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
        .head { background: #3c50e0; color: #ffffff; padding: 20px 24px; }
        .head h1 { margin: 0; font-size: 20px; }
        .head p { margin: 4px 0 0; opacity: .9; font-size: 13px; }
        .body { padding: 24px; }
        .badge { display: inline-block; background: #10b981; color: #ffffff; font-size: 12px; font-weight: bold; padding: 4px 10px; border-radius: 9999px; margin-bottom: 14px; }
        h2 { font-size: 16px; margin: 22px 0 10px; color: #111827; border-bottom: 1px solid #e5e7eb; padding-bottom: 6px; }
        table.dt { width: 100%; border-collapse: collapse; font-size: 13px; }
        table.dt td { padding: 6px 8px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
        table.dt td.k { width: 34%; font-weight: 600; color: #6b7280; }
        table.items { width: 100%; border-collapse: collapse; font-size: 13px; }
        table.items th { background: #f9fafb; text-align: left; padding: 7px 8px; border: 1px solid #e5e7eb; font-weight: 600; font-size: 12px; }
        table.items td { padding: 7px 8px; border: 1px solid #e5e7eb; vertical-align: top; }
        .total { text-align: right; font-size: 13px; margin-top: 14px; }
        .total b { font-size: 15px; color: #111827; }
        .btn { display: block; text-decoration: none; background: #3c50e0; color: #ffffff; font-weight: 600; text-align: center; padding: 12px 16px; border-radius: 6px; margin-top: 22px; font-size: 14px; }
        .foot { background: #f9fafb; padding: 14px 24px; font-size: 12px; color: #6b7280; }
        .alt { display: block; text-align: center; margin-top: 12px; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="head">
            <h1>New Quotation Request</h1>
            <p>{{ $quotation->quotation_number }} &middot; {{ $quotation->quotation_date->format('d M Y') }}</p>
        </div>
        <div class="body">
            <span class="badge">New Request Received</span>

            <p>A new quotation request has just been submitted on the website. Here are the details:</p>

            <h2>Customer / Applicant</h2>
            <table class="dt">
                <tr><td class="k">Name</td><td>{{ $quotation->client_name }}</td></tr>
                @if($quotation->company_name)<tr><td class="k">Company</td><td>{{ $quotation->company_name }}</td></tr>@endif
                <tr><td class="k">Email</td><td>{{ $quotation->email }}</td></tr>
                @if($quotation->phone)<tr><td class="k">Phone</td><td>{{ $quotation->phone }}</td></tr>@endif
                @if($quotation->preferred_contact_method)<tr><td class="k">Preferred Contact</td><td>{{ $quotation->preferred_contact_method }}</td></tr>@endif
                @if($quotation->gst_number)<tr><td class="k">GST Number</td><td>{{ $quotation->gst_number }}</td></tr>@endif
                @if($quotation->pan_number)<tr><td class="k">PAN Number</td><td>{{ $quotation->pan_number }}</td></tr>@endif
                @if($quotation->address)
                    <tr><td class="k">Address</td><td>{{ $quotation->address }}{{ $quotation->address_line2 ? ', ' . $quotation->address_line2 : '' }}{{ $quotation->city ? ', ' . $quotation->city : '' }}{{ $quotation->state ? ', ' . $quotation->state : '' }}{{ $quotation->country ? ', ' . $quotation->country : '' }}</td></tr>
                @endif
            </table>

            @if($quotation->sample_name || $quotation->sample_batch_no || $quotation->sample_physical_form || $quotation->sample_storage_condition)
                <h2>Sample Details</h2>
                <table class="dt">
                    @if($quotation->sample_name)<tr><td class="k">Sample Name</td><td>{{ $quotation->sample_name }}</td></tr>@endif
                    @if($quotation->sample_batch_no)<tr><td class="k">Batch No.</td><td>{{ $quotation->sample_batch_no }}</td></tr>@endif
                    @if($quotation->sample_physical_form)<tr><td class="k">Physical Form</td><td>{{ $quotation->sample_physical_form }}</td></tr>@endif
                    @if($quotation->sample_storage_condition)<tr><td class="k">Storage Condition</td><td>{{ $quotation->sample_storage_condition }}</td></tr>@endif
                </table>
            @endif

            <h2>Quotation Items</h2>
            @if($quotation->items->count())
                <table class="items">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th style="text-align:center">Qty</th>
                            <th style="text-align:right">Rate</th>
                            <th style="text-align:right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotation->items as $item)
                            <tr>
                                <td>{{ $item->service_name_snapshot }}</td>
                                <td style="text-align:center">{{ $item->quantity }}</td>
                                <td style="text-align:right">{{ $currency }} {{ number_format((float) $item->unit_price_snapshot, 2) }}</td>
                                <td style="text-align:right">{{ $currency }} {{ number_format((float) $item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No line items recorded.</p>
            @endif

            @if($quotation->project_description)<h2>Project Description</h2><p>{{ $quotation->project_description }}</p>@endif

            <div class="total">
                Subtotal: {{ $currency }} {{ number_format((float) $quotation->subtotal, 2) }}<br>
                @if((float) $quotation->discount > 0)Discount: - {{ $currency }} {{ number_format((float) $quotation->discount, 2) }}<br>@endif
                Tax: {{ $currency }} {{ number_format((float) $quotation->tax, 2) }}<br>
                <b>Total Amount: {{ $currency }} {{ number_format((float) $quotation->grand_total, 2) }}</b>
            </div>

            @if($quotation->expected_timeline || $quotation->additional_requirements || $quotation->notes)
                <h2>Additional Information</h2>
                <table class="dt">
                    @if($quotation->expected_timeline)<tr><td class="k">Expected Timeline</td><td>{{ $quotation->expected_timeline }}</td></tr>@endif
                    @if($quotation->additional_requirements)<tr><td class="k">Requirements</td><td>{!! nl2br(e($quotation->additional_requirements)) !!}</td></tr>@endif
                    @if($quotation->notes)<tr><td class="k">Notes</td><td>{!! nl2br(e($quotation->notes)) !!}</td></tr>@endif
                </table>
            @endif

            <a class="btn" href="{{ $adminUrl }}">View Quotation in Admin Panel</a>
            <span class="alt">The quotation PDF is attached to this email.</span>
        </div>
        <div class="foot">
            {{ $company['name'] ?? '' }} &middot; This email was sent automatically because a new quotation request was submitted.
        </div>
    </div>
</body>
</html>
