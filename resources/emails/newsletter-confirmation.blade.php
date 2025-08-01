<p>مرحباً {{ $subscriber->email }}،</p>
<p>يرجى الضغط على الرابط أدناه لتأكيد اشتراكك:</p>
<a href="{{ route('newsletter.confirm', $subscriber->token) }}">
    تأكيد الاشتراك
</a>
