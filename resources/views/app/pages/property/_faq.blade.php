<section class="community-info-v3 card-v3"> {{-- Or faq-section-v3 directly if not nested --}}
    <div class="faq-section-v3">
        <h2>{{ __('app/properties.faq.title') }}</h2>
        <div class="faq-list-v3">
            @foreach ($faqs as $faq_item)
                <div class="faq-item-v3">
                    <button class="faq-question-v3">
                        {{-- Assuming 'question' and 'answer' are translatable with Spatie/laravel-translatable --}}
                        <span>{{ $faq_item['question'] ?? __('app/properties.faq.question_default') }}</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer-v3">
                        {!! $faq_item['answer'] ?? '<p>' . __('app/properties.faq.answer_default') . '</p>' !!}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
