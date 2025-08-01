    <div id="reportPropertyModal" class="modal-v3">
        <div class="modal-content-v3">
            <span class="close-modal-v3" data-close-report-modal>×</span>
            <h3>{{ trans('app\properties.send_report_title') }}</h3>
            <form id="reportFormV3" class="allow-guest-submission">
                <div class="form-group-v3 user-details-group"> 
                    <label for="reportUserName">{{ trans('app/contact.name') }}:</label>
                    <input type="text" id="reportUserName" name="name" readonly>
                    <span class="form-error-message text-danger d-block" data-field="name"></span>
                </div>
                <div class="form-group-v3 user-details-group"> 
                    <label for="reportUserEmail">{{ trans('app/contact.email') }}:</label>
                    <input type="email" id="reportUserEmail" name="email" readonly>
                    <span class="form-error-message text-danger d-block" data-field="email"></span>
                </div>
                <div class="form-group-v3 user-details-group"> 
                    <label for="reportUserMobile">{{ trans('app/contact.mobile') }}:</label>
                    <input type="tel" id="reportUserMobile" name="mobile" readonly>
                    <span class="form-error-message text-danger d-block" data-field="mobile"></span>

                </div>
                <!-- End User Details -->

                <div class="form-group-v3">
                    <label for="reportReason">
                        {{ trans('app/properties.report_reason') }}:<span class="text-danger">*</span></label>
                    <select id="reportReason" name="reportReason" required>
                        <option value="">-- {{ trans('app/properties.report_reason_select_text') }} --</option>
                        @foreach ($reportTypes as $type)
                            <option value="{{ $type['id'] }}">
                                {{ $type['name'] }}
                            </option>
                        @endforeach
                    </select>
                    <span class="form-error-message text-danger d-block" data-field="type_id"></span>
                </div>
                <div class="form-group-v3">
                    <label for="reportDetails">{{ trans('app/properties.report_details_title') }}:</label>
                    <textarea id="reportDetails" name="reportDetails" rows="3"
                        placeholder="{{ trans('app/properties.please_write_report_details') }}"></textarea>
                    <span class="form-error-message text-danger d-block" data-field="message"></span>
                </div>
                <div class="confirm-actions-v3">
                    <button type="submit"
                        class="btn-v3 btn-v3-primary">{{ trans('app/properties.send_report_button_text') }}</button>
                    <button type="button" id="closeReportBtn" data-close-report-modal
                        class="btn-v3 btn-v3-outline">{{ trans('app/properties.cancel') }}</button>
                </div>
            </form>
        </div>
    </div>
    <div id="sharePropertyModal" class="modal-v3 share-modal-v3">
        <div class="modal-content-v3 share-modal-content-refined">
            <span class="close-modal-v3" data-close-share-modal>×</span>
            <h3>{{ trans('app/properties.share_property_title') }}</h3>
            <div class="share-options-v3">
                <div class="share-link-section-v3">
                    <input type="text" id="propertyShareLink" value="https://example.com/property/123" readonly>
                    <button id="copyShareLinkBtn" class="btn-v3 btn-v3-outline copy-action-btn">
                        <i class="far fa-copy"></i> <span
                            class="copy-btn-text">{{ trans('app/properties.copy_link') }}</span>
                    </button>
                </div>
                <p class="share-via-text-v3">{{ trans('app/properties.or_share_via') }}</p>
                <div class="social-share-list-v3">
                    <a href="#" id="shareViaWhatsApp" class="social-share-item-v3 whatsapp" target="_blank"
                        rel="noopener noreferrer"><i class="fab fa-whatsapp"></i>
                        <span>{{ trans('app/properties.whatsapp') }}</span></a>
                    <a href="#" id="shareViaTwitter" class="social-share-item-v3 twitter" target="_blank"
                        rel="noopener noreferrer"><i class="fab fa-twitter"></i>
                        <span>{{ trans('app/properties.twitter') }}</span></a>
                    <a href="#" id="shareViaFacebook" class="social-share-item-v3 facebook" target="_blank"
                        rel="noopener noreferrer"><i class="fab fa-facebook-f"></i>
                        <span>{{ trans('app/properties.facebook') }}</span></a>
                    <a href="#" id="shareViaEmail" class="social-share-item-v3 email"><i
                            class="fas fa-envelope"></i> <span>{{ trans('app/properties.email') }}</span></a>
                    <a href="#" id="shareViaLinkedIn" class="social-share-item-v3 linkedin" target="_blank"
                        rel="noopener noreferrer"><i class="fab fa-linkedin-in"></i>
                        <span>{{ trans('app/properties.linkedin') }}</span></a>
                    <a href="#" id="shareViaTelegram" class="social-share-item-v3 telegram" target="_blank"
                        rel="noopener noreferrer"><i class="fab fa-telegram-plane"></i>
                        <span>{{ trans('app/properties.telegram') }}</span></a>
                </div>
            </div>
        </div>
    </div>
    <div id="showMobileModal" class="modal-v3">
        <div class="modal-content-v3 mobile-modal-content-v3">
            <span class="close-modal-v3" data-close-mobile-modal>×</span>
            <h3>{{ trans('app/properties.agent_phone_number') }}</h3>
            <div class="mobile-number-display-v3">
                <div class="agent-number-input-like-v3">
                    <span id="agentMobileNumberDisplay">{{ trans('app/properties.loading') }}</span>
                    <button id="copyMobileNumberBtnV3" class="copy-icon-btn-v3"
                        aria-label="{{ trans('app/properties.copy_number') }}">
                        <i class="far fa-copy"></i>
                        <span class="copied-feedback-v3">{{ trans('app/properties.copied_feedback') }}</span>
                    </button>
                </div>
                <a href="#" id="callAgentLink" class="btn-v3 btn-v3-primary call-now-btn">
                    <i class="fas fa-phone-alt"></i>
                    <span>{{ trans('app/properties.call_now') }}</span>
                </a>
            </div>
            <p class="modal-note-v3">{{ trans('app/properties.call_note') }}</p>
        </div>
    </div>
    <div id="blacklistConfirmModal" class="modal-v3">
        <div class="modal-content-v3 confirm-modal-content-v3">
            <span class="close-modal-v3" data-close-blacklist-modal>×</span>
            <h3>{{ trans('app/properties.blacklist_confirm_title') }}</h3>
            <p id="blacklistConfirmMessage">{{ trans('app/properties.blacklist_confirm_message') }}</p>
            <div class="confirm-actions-v3">
                <button id="confirmBlacklistBtn"
                    class="btn-v3 btn-v3-danger">{{ trans('app/properties.blacklist_confirm_yes') }}</button>
                <button id="cancelBlacklistBtn"
                    class="btn-v3 btn-v3-outline">{{ trans('app/properties.blacklist_confirm_cancel') }}</button>
            </div>
        </div>
    </div>
