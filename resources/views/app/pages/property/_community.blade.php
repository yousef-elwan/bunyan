         <section class="community-info-v3 card-v3">
             <div class="rating-summary-section-v3">
                 <h2>تقييمات العقار (<span id="totalRatingCountV3">0</span>)</h2>
                 <div class="rating-overview-v3">
                     <div class="average-rating-v3">
                         <span class="average-stars-v3" id="averageRatingValueV3">0.0</span>
                         <div class="stars-display-v3" id="averageStarsDisplayV3">
                             <i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i
                                 class="far fa-star"></i><i class="far fa-star"></i>
                         </div>
                         <p>متوسط التقييم بناءً على <span id="averageRatingTotalReviewsV3">0</span>
                             مراجعة</p>
                     </div>
                     <div class="rating-breakdown-v3" id="ratingBreakdownContainerV3">
                     </div>
                 </div>
                 <button class="btn-v3 btn-v3-outline btn-add-review-v3">إضافة
                     تقييمك</button>
             </div>

             <div class="comments-section-v3">
                 <h2>التعليقات والمراجعات (<span id="totalCommentCountV3">0</span>)</h2>
                 <div class="comment-list-v3" id="commentListV3">
                 </div>
                 <button class="btn-v3 btn-v3-link load-more-comments-v3" id="loadMoreCommentsV3" style="display:none;">
                     <span class="btn-text">تحميل المزيد</span>
                 </button>
                 <p class="no-more-comments-v3" id="noMoreCommentsMessageV3" style="display:none;">لا يوجد
                     المزيد من
                     التعليقات.</p>
             </div>

             <form id="addCommentFormV3" class="add-comment-form-v3">
                 <h4>إضافة تعليق أو تقييم</h4>
                 <div class="form-group-v3 your-rating-v3">
                     <label for="userRatingInputV3">تقييمك:</label>
                     <div class="star-rating-input-v3" id="formStarRatingV3">
                         <i class="far fa-star" data-value="1"></i>
                         <i class="far fa-star" data-value="2"></i>
                         <i class="far fa-star" data-value="3"></i>
                         <i class="far fa-star" data-value="4"></i>
                         <i class="far fa-star" data-value="5"></i>
                     </div>
                     <input type="hidden" name="userRating" id="userRatingInputV3" value="0">
                 </div>
                 <div class="form-group-v3">
                     <label for="commenterNameV3">الاسم:</label>
                     <input type="text" id="commenterNameV3" name="commenterName" class="input-v3"
                         placeholder="اسمك (اختياري)">
                 </div>
                 <div class="form-group-v3">
                     <label for="commentTextV3">تعليقك /
                         مراجعتك:</label>
                     <textarea name="commentText" id="commentTextV3" class="input-v3" rows="4" placeholder="اكتب هنا..." required></textarea>
                 </div>
                 <div style="display: flex; justify-content: flex-start; gap: 10px;">
                     <button type="submit" class="btn-v3 btn-v3-primary">
                         <span class="btn-text">إرسال</span>
                     </button>
                     <button type="button" class="btn-v3 btn-v3-link btn-cancel-comment-review-v3">إلغاء</button>
                 </div>
             </form>
         </section>
