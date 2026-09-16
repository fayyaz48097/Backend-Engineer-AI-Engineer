{{--
    The body is AI-generated text. It is escaped with e() before nl2br() so a
    model that emits HTML (or a prompt-injected customer message that talks it
    into emitting HTML) cannot inject markup into a customer's inbox.
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $ticket->email_subject }}</title>
</head>
<body style="margin:0; padding:24px; background:#f5f5f5; font-family:-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif; color:#1a1a1a;">
    <div style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:8px; padding:32px;">
        <p style="margin:0 0 16px; font-size:15px; line-height:1.6;">
            {!! nl2br(e($body)) !!}
        </p>

        <hr style="border:none; border-top:1px solid #e5e5e5; margin:24px 0;">

        <p style="margin:0; font-size:12px; color:#888;">
            Reference: ticket #{{ $ticketId }}
        </p>
    </div>
</body>
</html>
