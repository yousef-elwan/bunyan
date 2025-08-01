import { translate } from "../../utils/helpers";

export function initCommunityFeatures() {
    // --- Global/Helper Variables & Functions ---
    const loadingSpinnerHtml = '<span class="loading-spinner-container"><i class="fas fa-spinner loading-spinner-v3"></i></span>';
    const initialCommentLoadingMessageId = 'initialCommentLoadingMessageV3'; // Renamed for V3 convention

    function toggleButtonLoading(buttonElement, isLoading, newContentHtmlIfRestoring = null) {
        if (!buttonElement) return;

        if (isLoading) {
            if (!buttonElement.dataset.originalContent) {
                buttonElement.dataset.originalContent = buttonElement.innerHTML;
            }
            buttonElement.innerHTML = loadingSpinnerHtml;
            buttonElement.classList.add('loading');
            buttonElement.disabled = true;
        } else {
            if (newContentHtmlIfRestoring !== null) {
                buttonElement.innerHTML = newContentHtmlIfRestoring;
            } else if (buttonElement.dataset.originalContent) {
                buttonElement.innerHTML = buttonElement.dataset.originalContent;
            }

            if (buttonElement.innerHTML === loadingSpinnerHtml || buttonElement.innerHTML === "") {
                if (buttonElement.classList.contains('load-more-comments-v3') || buttonElement.id === 'loadMoreCommentsV3') {
                    buttonElement.innerHTML = '<span class="btn-text">' + translate('load_more') + '</span>';
                } else if (buttonElement.type === 'submit') {
                    buttonElement.innerHTML = '<span class="btn-text">' + translate('send') + '</span>';
                }
            }

            buttonElement.classList.remove('loading');
            buttonElement.disabled = false;
            delete buttonElement.dataset.originalContent;
        }
    }

    // --- Tabs Functionality ---
    const tabButtons = document.querySelectorAll('.community-tab-button-v3');
    const tabContents = document.querySelectorAll('.community-tab-content-v3');
    const addCommentForm = document.getElementById('addCommentFormV3'); // Using V3 ID

    tabButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            tabButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            const targetTabId = button.dataset.tab; // e.g., "faqContentV3", "reviewsAndCommentsContentV3"
            tabContents.forEach(content => {
                content.classList.remove('active');
                if (content.id === targetTabId) {
                    content.classList.add('active');
                }
            });
            // Hide comment form if active and a tab is switched (and not by clicking the add review button itself)
            if (addCommentForm && !event.target.classList.contains('btn-add-review-v3') &&
                (!addCommentForm.contains(event.target) && addCommentForm.classList.contains('active'))) {
                addCommentForm.classList.remove('active');
            }
        });
    });

    // --- FAQ Accordion ---
    const faqItems = document.querySelectorAll('.faq-item-v3');
    // // console.log('FAQ Items Found:', faqItems.length); // DEBUG: Check if items are found

    faqItems.forEach(item => {
        const questionButton = item.querySelector('.faq-question-v3');
        const answerDiv = item.querySelector('.faq-answer-v3');

        // // console.log('Processing FAQ item. Button:', questionButton, 'Answer:', answerDiv); // DEBUG

        if (questionButton && answerDiv) {
            questionButton.addEventListener('click', () => {
                // // console.log('FAQ Question Clicked:', item); // DEBUG: Check if click is registered

                const wasActive = item.classList.contains('active');
                // // console.log("wasActive", wasActive);
                // // console.log('Button:', questionButton, 'Answer:', answerDiv); // DEBUG

                // First, close all other active items
                faqItems.forEach(otherItem => {
                    if (otherItem !== item && otherItem.classList.contains('active')) {
                        // // console.log('Closing other active FAQ:', otherItem); // DEBUG
                        otherItem.classList.remove('active');
                        const otherAnswerDiv = otherItem.querySelector('.faq-answer-v3');
                        if (otherAnswerDiv) {
                            otherAnswerDiv.style.maxHeight = null;
                        }
                    }
                });

                // Now, toggle the current item
                if (!wasActive) {
                    // // console.log('Opening FAQ:', item); // DEBUG
                    item.classList.add('active');
                    answerDiv.style.maxHeight = answerDiv.scrollHeight + "px";
                } else {
                    // // console.log('Closing FAQ:', item); // DEBUG
                    item.classList.remove('active');
                    answerDiv.style.maxHeight = null;
                }
            });
        } else {
            // console.warn('FAQ item missing question button or answer div:', item); // DEBUG
        }
    });


    // --- Star Rating Input (Generalized for multiple instances if needed) ---
    function initializeStarRating(starContainerSelector, hiddenInputSelector) {
        const starContainers = document.querySelectorAll(starContainerSelector);
        starContainers.forEach(starContainer => {
            const hiddenInput = starContainer.id === 'formStarRatingV3' // Specific handling for main form
                ? document.getElementById('userRatingInputV3')
                : starContainer.nextElementSibling; // Generic for others

            if (!hiddenInput) {
                console.warn('Star rating hidden input not found for container:', starContainer);
                return;
            }
            const stars = starContainer.querySelectorAll('i');

            stars.forEach(star => {
                star.addEventListener('click', function () {
                    const value = this.dataset.value;
                    hiddenInput.value = value;
                    updateStarsVisual(stars, value);
                });
                star.addEventListener('mouseover', function () {
                    updateStarsVisual(stars, this.dataset.value, true);
                });
                star.addEventListener('mouseout', function () {
                    updateStarsVisual(stars, hiddenInput.value);
                });
            });
        });
    }

    function updateStarsVisual(starsCollection, value, isHover = false) {
        starsCollection.forEach(s => {
            const starValue = parseInt(s.dataset.value);
            s.classList.remove('fas', 'far', 'selected', 'hovered');
            if (starValue <= parseInt(value)) {
                s.classList.add('fas');
                if (!isHover) s.classList.add('selected');
                else s.classList.add('hovered');
            } else {
                s.classList.add('far');
            }
        });
    }

    function resetStarsVisual(starContainer) {
        if (!starContainer) return;
        const stars = starContainer.querySelectorAll('i');
        stars.forEach(s => {
            s.classList.remove('fas', 'selected', 'hovered');
            s.classList.add('far');
        });
    }
    // Initialize star rating for the main comment form
    // The selector for star container within the form should be specific enough, e.g., #addCommentFormV3 .star-rating-input-v3
    // For simplicity, using the ID directly as in community_features-2.html if that form has a specific star container ID.
    // Let's assume the star container inside `addCommentFormV3` has ID `formStarRatingV3`
    if (document.getElementById('formStarRatingV3') && document.getElementById('userRatingInputV3')) {
        initializeStarRating('#formStarRatingV3', '#userRatingInputV3');
    }
    // If there are other generic star rating inputs:
    // initializeStarRating('.star-rating-input-v3.some-other-context', null); // null would use nextElementSibling logic


    // --- Rating Summary & Breakdown ---
    const averageRatingValueEl = document.getElementById('averageRatingValueV3');
    const averageStarsDisplayEl = document.getElementById('averageStarsDisplayV3');
    const averageRatingTotalReviewsEl = document.getElementById('averageRatingTotalReviewsV3');
    const totalRatingCountEl = document.getElementById('totalRatingCountV3');
    const ratingBreakdownContainer = document.getElementById('ratingBreakdownContainerV3');
    let currentRatingsData = { average: 0, totalReviews: 0, breakdown: [{ stars: 5, count: 0 }, { stars: 4, count: 0 }, { stars: 3, count: 0 }, { stars: 2, count: 0 }, { stars: 1, count: 0 }] };

    function calculateRatingStats(commentsArray) {
        if (!commentsArray || commentsArray.length === 0) return { average: 0, totalReviews: 0, breakdown: [{ stars: 5, count: 0 }, { stars: 4, count: 0 }, { stars: 3, count: 0 }, { stars: 2, count: 0 }, { stars: 1, count: 0 }] };
        let totalRatingSum = 0;
        let ratedReviewsCount = 0;
        const breakdown = [{ stars: 5, count: 0 }, { stars: 4, count: 0 }, { stars: 3, count: 0 }, { stars: 2, count: 0 }, { stars: 1, count: 0 }];

        function countRatingsRecursive(comments) {
            comments.forEach(comment => {
                if (comment.rating && comment.rating > 0 && comment.parentId === null) { // Only top-level comments for average
                    totalRatingSum += comment.rating;
                    ratedReviewsCount++;
                    if (comment.rating >= 1 && comment.rating <= 5) {
                        breakdown[5 - comment.rating].count++;
                    }
                }
            });
        }
        countRatingsRecursive(commentsArray);
        const average = ratedReviewsCount > 0 ? (totalRatingSum / ratedReviewsCount) : 0;
        return { average: average, totalReviews: ratedReviewsCount, breakdown: breakdown };
    }

    function displayRatings() {
        currentRatingsData = calculateRatingStats(allComments); // allComments defined below
        if (averageRatingValueEl) averageRatingValueEl.textContent = currentRatingsData.average.toFixed(1);
        if (averageRatingTotalReviewsEl) averageRatingTotalReviewsEl.textContent = currentRatingsData.totalReviews;
        if (totalRatingCountEl) totalRatingCountEl.textContent = currentRatingsData.totalReviews;

        if (averageStarsDisplayEl) {
            averageStarsDisplayEl.innerHTML = '';
            let rating = currentRatingsData.average;
            for (let i = 1; i <= 5; i++) {
                const starIcon = document.createElement('i');
                if (rating >= 0.75) { starIcon.className = 'fas fa-star'; rating--; }
                else if (rating >= 0.25) { starIcon.className = 'fas fa-star-half-alt'; rating = 0; }
                else { starIcon.className = 'far fa-star'; }
                averageStarsDisplayEl.appendChild(starIcon);
            }
        }
        if (ratingBreakdownContainer) {
            ratingBreakdownContainer.innerHTML = '';
            currentRatingsData.breakdown.forEach(item => {
                const percentage = currentRatingsData.totalReviews > 0 ? (item.count / currentRatingsData.totalReviews) * 100 : 0;
                const row = document.createElement('div');
                row.className = 'rating-bar-row-v3';
                row.innerHTML = `<span>${item.stars} ${item.stars === 1 ? translate('star_1') : (item.stars === 2 ? translate('star_2') : translate('stars'))}</span><div class="rating-bar-v3"><div class="rating-bar-filled-v3" style="width: ${percentage.toFixed(1)}%;"></div></div><span>(${item.count})</span>`;
                ratingBreakdownContainer.appendChild(row);
            });
        }
    }

    // --- Comments Functionality ---
    const commentListEl = document.getElementById('commentListV3'); // Adapted ID
    const loadMoreCommentsBtn = document.getElementById('loadMoreCommentsV3'); // Adapted ID
    const noMoreCommentsMessage = document.getElementById('noMoreCommentsMessageV3'); // Adapted ID
    const totalCommentCountTitleEl = document.getElementById('totalCommentCountV3'); // For comments section title
    let currentReplyForm = null;

    // DEMO DATA (Should be replaced with actual data fetching in a real application)
    let allComments = [
        { id: 1, author: 'أحمد محمد', avatar: 'https://placehold.co/36x36/778899/FFFFFF?text=A', rating: 5, text: 'فيلا رائعة جداً!\nالموقع ممتاز والتشطيبات فاخرة.\nأنصح بها بشدة للعائلات الكبيرة.', date: 'منذ يومين', parentId: null, replies: [{ id: 101, author: 'مجموعة الثقة (الوكيل)', avatar: 'https://placehold.co/32x32/0062cc/FFFFFF?text=و', rating: 0, text: 'شكراً جزيلاً لتقييمك الرائع أستاذ أحمد! نسعد بخدمتك.', date: 'منذ يوم', parentId: 1, replies: [] }] },
        { id: 2, author: 'سارة علي', avatar: 'https://placehold.co/36x36/AACCFF/000000?text=S', rating: 4, text: 'تجربة جيدة بشكل عام، بعض الملاحظات البسيطة على الصيانة ولكن السعر مناسب.', date: 'منذ أسبوع', parentId: null, replies: [{ id: 201, author: 'مجموعة الثقة (الوكيل)', avatar: 'https://placehold.co/32x32/0062cc/FFFFFF?text=و', rating: 0, text: 'شكراً لملاحظاتك سارة، نعمل على تحسين خدمات الصيانة باستمرار.', date: 'منذ 6 أيام', parentId: 2, replies: [] }] },
        { id: 3, author: 'خالد عبدالله', avatar: 'https://placehold.co/36x36/FFD700/000000?text=K', rating: 5, text: 'كل شيء كان مثالياً، من الحجز إلى المغادرة. خدمة العملاء ممتازة.', date: 'منذ 3 أسابيع', parentId: null, replies: [] },
        { id: 4, author: 'نورة إبراهيم', avatar: 'https://placehold.co/36x36/E6E6FA/000000?text=N', rating: 3, text: 'الموقع جيد ولكن الأثاث يحتاج إلى تحديث بسيط. السعر معقول.', date: 'منذ شهر', parentId: null, replies: [{ id: 102, author: 'أحمد محمد', avatar: 'https://placehold.co/32x32/778899/FFFFFF?text=A', rating: 0, text: 'أتفق معكِ بشأن الأثاث، لكن الموقع يعوض ذلك.', date: 'منذ 3 أسابيع', parentId: 4, replies: [{ id: 103, author: 'نورة إبراهيم', avatar: 'https://placehold.co/30x30/E6E6FA/000000?text=N', rating: 0, text: 'صحيح، سأفكر في الأمر مرة أخرى.', date: 'منذ أسبوعين', parentId: 102, replies: [] }] }] },
        { id: 5, author: 'عمر ياسين', avatar: 'https://placehold.co/36x36/228B22/FFFFFF?text=O', rating: 4, text: 'المكان نظيف وهادئ جداً، مثالي للاسترخاء. الواي فاي كان ضعيفاً بعض الشيء.', date: 'منذ شهرين', parentId: null, replies: [] },
        { id: 6, author: 'فاطمة حسن', avatar: 'https://placehold.co/36x36/FFC0CB/000000?text=F', rating: 5, text: 'أفضل إقامة قضيتها على الإطلاق! الموظفون ودودون والمرافق ممتازة.', date: 'منذ 3 شهور', parentId: null, replies: [] }
    ];
    let commentsDisplayedCount = 0;
    const commentsPerLoad = 2;

    function createCommentElement(comment, level = 0) {
        const item = document.createElement('div');
        item.className = 'comment-item-v3';
        item.dataset.commentId = comment.id;
        const indent = level * 25;
        const marginSide = document.documentElement.getAttribute('dir') === 'rtl' ? 'marginRight' : 'marginLeft';
        if (level > 0) item.style[marginSide] = `${indent}px`;

        let ratingStarsHtml = '';
        if (comment.rating && comment.rating > 0) {
            for (let i = 1; i <= 5; i++) {
                ratingStarsHtml += `<i class="${i <= comment.rating ? 'fas' : 'far'} fa-star"></i>`;
            }
        }
        const avatarSize = Math.max(24, 36 - (level * 4));
        let repliesButtonHtml = '';
        if (comment.replies && comment.replies.length > 0 && level < 2) { // Max 2 levels of "view replies" button
            const replyText = comment.replies.length === 1 ? translate('replay_1') : (comment.replies.length === 2 ? translate('replay_2') : `${comment.replies.length} ` + translate('replaies'));
            repliesButtonHtml = `<button class="comment-action-btn-v3 btn-view-replies" data-comment-id="${comment.id}"><i class="fas fa-comments"></i> ${replyText}</button>`;
        }
        item.innerHTML = `
            <div class="comment-author-v3">
                <img src="${comment.avatar}" alt="${comment.author}" class="comment-avatar-v3" style="width:${avatarSize}px; height:${avatarSize}px;">
                <div>
                    <span class="comment-author-name-v3">${comment.author}</span>
                    ${comment.rating > 0 ? `<div class="comment-author-rating-v3 stars-display-v3">${ratingStarsHtml}</div>` : ''}
                </div>
            </div>
            <p class="comment-text-v3">${comment.text.replace(/\n/g, '<br>')}</p>
            <div class="comment-meta-v3">
                <span class="comment-date-v3">${comment.date}</span>
                <div class="comment-actions-group">
                    ${repliesButtonHtml}
                    ${level < 2 ? `<button class="comment-action-btn-v3 btn-show-reply-form reply-btn-v3" data-parent-id="${comment.id}"><i class="fas fa-reply"></i> <span>رد</span></button>` : ''}
                </div>
            </div>
            <div class="comment-replies-container-v3" style="display: none;"></div>
            <div class="reply-form-container-v3" style="display:none;"></div>`;

        const viewRepliesButton = item.querySelector('.btn-view-replies');
        if (viewRepliesButton) {
            viewRepliesButton.addEventListener('click', function () {
                const parentId = parseInt(this.dataset.commentId);
                const repliesContainer = item.querySelector('.comment-replies-container-v3');
                toggleRepliesVisibility(repliesContainer, parentId, this);
            });
        }
        const replyButton = item.querySelector('.btn-show-reply-form');
        if (replyButton) {
            replyButton.addEventListener('click', function () {
                const parentId = this.dataset.parentId;
                const replyFormContainer = item.querySelector('.reply-form-container-v3');
                toggleReplyForm(replyFormContainer, parentId);
            });
        }
        return item;
    }

    function findCommentById(commentsArray, id) {
        for (let comment of commentsArray) {
            if (comment.id === id) return comment;
            if (comment.replies && comment.replies.length > 0) {
                const found = findCommentById(comment.replies, id);
                if (found) return found;
            }
        }
        return null;
    }

    async function toggleRepliesVisibility(repliesContainer, parentId, buttonElement) {
        if (!repliesContainer || !buttonElement) return;
        const parentCommentData = findCommentById(allComments, parentId);
        if (!parentCommentData || !parentCommentData.replies || parentCommentData.replies.length === 0) return;

        const isCurrentlyVisible = repliesContainer.style.display === 'block';
        const replyTextPlural = comment.replies.length === 1 ? translate('replay_1') : (comment.replies.length === 2 ? translate('replay_2') : `${comment.replies.length} ` + translate('replaies'));
        if (isCurrentlyVisible) {
            repliesContainer.style.display = 'none';
            repliesContainer.innerHTML = ''; // Clear replies when hiding
            toggleButtonLoading(buttonElement, false, `<i class="fas fa-comments"></i> ${replyTextPlural}`);
            buttonElement.classList.remove('replies-shown');
        } else {
            toggleButtonLoading(buttonElement, true);
            await new Promise(resolve => setTimeout(resolve, 500)); // Simulate loading

            repliesContainer.innerHTML = ''; // Clear previous if any (though unlikely)
            let parentLevel = 0;
            const parentCommentElement = buttonElement.closest('.comment-item-v3');
            const marginSide = document.documentElement.getAttribute('dir') === 'rtl' ? 'marginRight' : 'marginLeft';
            if (parentCommentElement && parentCommentElement.style[marginSide]) {
                parentLevel = (parseInt(parentCommentElement.style[marginSide]) / 25) || 0;
            }
            parentCommentData.replies.forEach(reply => {
                repliesContainer.appendChild(createCommentElement(reply, parentLevel + 1));
            });
            repliesContainer.style.display = 'block';
            toggleButtonLoading(buttonElement, false, `<i class="fas fa-comment-slash"></i> ` + translate('hide_replies'));
            buttonElement.classList.add('replies-shown');
        }
    }

    function toggleReplyForm(container, parentId) {
        if (currentReplyForm && currentReplyForm !== container) { // Hide any other open reply form
            currentReplyForm.style.display = 'none';
            currentReplyForm.innerHTML = '';
        }

        if (container.style.display === 'none' || currentReplyForm !== container) {
            container.innerHTML = `
                <form class="add-reply-form-v3" data-parent-id="${parentId}">
                    <textarea name="replyText" rows="2" placeholder="${translate('please_write_commit')}" required class="input-v3"></textarea>
                    <div class="reply-form-actions">
                        <button type="submit" class="btn-v3 btn-v3-primary btn-sm-v3">
                            <span class="btn-text">`+ translate('submit_reply') + `</span>
                        </button>
                        <button type="button" class="btn-v3 btn-v3-link btn-sm-v3 btn-cancel-reply">`+ translate('cancel') + `</button>
                    </div>
                </form>`;
            container.style.display = 'block';
            container.querySelector('textarea').focus();
            currentReplyForm = container;

            const replyForm = container.querySelector('.add-reply-form-v3');
            replyForm.addEventListener('submit', handleReplySubmit);
            replyForm.querySelector('.btn-cancel-reply').addEventListener('click', () => {
                container.style.display = 'none';
                container.innerHTML = '';
                currentReplyForm = null;
            });
        } else { // Clicked on the same reply button again to close
            container.style.display = 'none';
            container.innerHTML = '';
            currentReplyForm = null;
        }
    }

    async function handleReplySubmit(event) {
        event.preventDefault();
        const form = event.target;
        const submitButton = form.querySelector('button[type="submit"]');
        const parentId = parseInt(form.dataset.parentId);
        const replyText = form.querySelector('textarea[name="replyText"]').value;

        if (replyText.trim() === '') {
            alert(translate('please_write_reply'));
            return;
        }
        toggleButtonLoading(submitButton, true);
        await new Promise(resolve => setTimeout(resolve, 700)); // Simulate network

        const newReply = {
            id: Date.now(), // Simple unique ID
            author: translate('you_visitor'), // Placeholder name
            avatar: `https://placehold.co/32x32/999999/FFFFFF?text=U`,
            rating: 0, // Replies typically don't have ratings
            text: replyText,
            date: 'الآن',
            parentId: parentId,
            replies: []
        };

        addReplyToData(newReply, parentId);
        addReplyToDOM(newReply, parentId); // Add to the DOM
        if (totalCommentCountTitleEl) totalCommentCountTitleEl.textContent = countAllComments(allComments); // Update total count if displayed in title

        toggleButtonLoading(submitButton, false); // Restore button
        if (form.parentElement) { // Remove form
            form.parentElement.style.display = 'none';
            form.parentElement.innerHTML = '';
        }
        currentReplyForm = null;
    }

    function addReplyToData(reply, parentId) {
        function findAndAdd(commentsArray) {
            for (let comment of commentsArray) {
                if (comment.id === parentId) {
                    if (!comment.replies) comment.replies = [];
                    comment.replies.unshift(reply); // Add to the beginning
                    return true;
                }
                if (comment.replies && comment.replies.length > 0) {
                    if (findAndAdd(comment.replies)) return true;
                }
            }
            return false;
        }
        findAndAdd(allComments);
    }

    function addReplyToDOM(replyData, parentId) {
        const parentCommentElement = commentListEl.querySelector(`.comment-item-v3[data-comment-id="${parentId}"]`);
        if (parentCommentElement) {
            const repliesContainer = parentCommentElement.querySelector('.comment-replies-container-v3');
            let parentLevel = 0;
            const marginSide = document.documentElement.getAttribute('dir') === 'rtl' ? 'marginRight' : 'marginLeft';
            const currentMargin = parentCommentElement.style[marginSide] || "0px";
            parentLevel = (parseInt(currentMargin) / 25) || 0;

            repliesContainer.prepend(createCommentElement(replyData, parentLevel + 1));

            // If replies were hidden, show them and update button
            if (repliesContainer.style.display === 'none') {
                repliesContainer.style.display = 'block';
                const viewRepliesBtn = parentCommentElement.querySelector('.btn-view-replies');
                if (viewRepliesBtn) {
                    const parentCommentData = findCommentById(allComments, parentId);
                    viewRepliesBtn.innerHTML = `<i class="fas fa-comment-slash"></i> ` + translate('hide_replies');
                    viewRepliesBtn.classList.add('replies-shown');
                    // Update reply count on button if it was just "X replies"
                    if (parentCommentData && parentCommentData.replies && parentCommentData.replies.length > 0) {
                        const replyText = parentCommentData.replies.length === 1 ? translate('replay_1') : (parentCommentData.replies.length === 2 ? translate('replay_2') : `${parentCommentData.replies.length} ` + translate('replaies'));
                        // This part can be tricky; let's assume the toggleVisibility handles the text correctly on its own after this
                    }
                }
            } else { // Replies container was already visible, update count on "View/Hide Replies" button
                const viewRepliesBtn = parentCommentElement.querySelector('.btn-view-replies');
                if (viewRepliesBtn) {
                    const parentCommentData = findCommentById(allComments, parentId);
                    if (parentCommentData && parentCommentData.replies && parentCommentData.replies.length > 0) {
                        const replyText = parentCommentData.replies.length === 1 ? translate('replay_1') : (parentCommentData.replies.length === 2 ? translate('replay_2') : `${parentCommentData.replies.length} ` + translate('replaies'));
                        if (viewRepliesBtn.classList.contains('replies-shown')) {
                            viewRepliesBtn.innerHTML = `<i class="fas fa-comment-slash"></i> ` + translate('hide_replies'); // Or update count if needed
                        } else {
                            viewRepliesBtn.innerHTML = `<i class="fas fa-comments"></i> ${replyText}`;
                        }
                    }
                }
            }
        }
    }

    function countAllComments(commentsArray) {
        let count = 0;
        function counter(comments) {
            comments.forEach(comment => {
                count++;
                if (comment.replies && comment.replies.length > 0) {
                    counter(comment.replies);
                }
            });
        }
        counter(commentsArray);
        return count;
    }

    async function loadComments() {
        if (!commentListEl) return;
        const isInitialLoad = commentsDisplayedCount === 0 &&
            (commentListEl.children.length === 0 ||
                (commentListEl.children.length === 1 && commentListEl.children[0].id === initialCommentLoadingMessageId));

        if (isInitialLoad) {
            if (!document.getElementById(initialCommentLoadingMessageId)) {
                commentListEl.innerHTML = `<p style="text-align:center; padding:10px;" id="${initialCommentLoadingMessageId}"> ${translate('loading_more_comments')} ${loadingSpinnerHtml}</p>`;
            }
        } else if (loadMoreCommentsBtn) {
            toggleButtonLoading(loadMoreCommentsBtn, true);
        }

        await new Promise(resolve => setTimeout(resolve, 800)); // Simulate network delay

        const initialLoadingMsgEl = document.getElementById(initialCommentLoadingMessageId);
        if (initialLoadingMsgEl) initialLoadingMsgEl.remove();

        if (loadMoreCommentsBtn && !isInitialLoad) {
            toggleButtonLoading(loadMoreCommentsBtn, false, '<span class="btn-text">' + translate('load_more') + '</span>');
        }

        const fragment = document.createDocumentFragment();
        const topLevelComments = allComments.filter(c => c.parentId === null);
        const nextComments = topLevelComments.slice(commentsDisplayedCount, commentsDisplayedCount + commentsPerLoad);

        if (nextComments.length === 0 && commentsDisplayedCount === 0) { // No comments at all
            if (commentListEl.innerHTML.trim() === '') { // Ensure not to overwrite existing "no comments" message if it's already there.
                commentListEl.innerHTML = '<p class="no-more-comments-v3" style="display:block; text-align:center;">' + translate('no_comments_yet') + '</p>';
            }
            if (loadMoreCommentsBtn) loadMoreCommentsBtn.style.display = 'none';
            if (noMoreCommentsMessage) noMoreCommentsMessage.style.display = 'none'; // No need for "no more" if there were none to begin with
        } else {
            nextComments.forEach(comment => fragment.appendChild(createCommentElement(comment, 0)));
            commentListEl.appendChild(fragment);
        }

        commentsDisplayedCount += nextComments.length;

        if (commentsDisplayedCount >= topLevelComments.length) {
            if (loadMoreCommentsBtn) loadMoreCommentsBtn.style.display = 'none';
            if (noMoreCommentsMessage && topLevelComments.length > 0) { // Show "no more" only if there were comments
                noMoreCommentsMessage.style.display = 'block';
            } else if (noMoreCommentsMessage) {
                noMoreCommentsMessage.style.display = 'none';
            }
        } else {
            if (loadMoreCommentsBtn && topLevelComments.length > 0) loadMoreCommentsBtn.style.display = 'block';
            if (noMoreCommentsMessage) noMoreCommentsMessage.style.display = 'none';
        }
        if (totalCommentCountTitleEl) totalCommentCountTitleEl.textContent = countAllComments(allComments);
    }

    if (loadMoreCommentsBtn) { loadMoreCommentsBtn.addEventListener('click', loadComments); }

    // --- Add Comment/Review Form (Main Form) ---
    // addCommentForm is already defined above (for tab switching logic)
    // const addCommentForm = document.getElementById('addCommentFormV3'); // Already got this
    const toggleReviewFormButtons = document.querySelectorAll('.btn-add-review-v3'); // Changed selector
    const cancelCommentReviewBtn = addCommentForm ? addCommentForm.querySelector('.btn-cancel-comment-review-v3') : null;

    toggleReviewFormButtons.forEach(button => {
        button.addEventListener('click', () => {
            if (addCommentForm) {
                addCommentForm.classList.add('active'); // Show form
                addCommentForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
                const textarea = addCommentForm.querySelector('textarea[name="commentText"]');
                if (textarea) textarea.focus();
            }
        });
    });

    if (cancelCommentReviewBtn && addCommentForm) {
        cancelCommentReviewBtn.addEventListener('click', () => {
            addCommentForm.classList.remove('active'); // Hide form
            addCommentForm.reset();
            const formStarRatingContainer = document.getElementById('formStarRatingV3');
            if (formStarRatingContainer) resetStarsVisual(formStarRatingContainer);
            const userRatingInput = document.getElementById('userRatingInputV3');
            if (userRatingInput) userRatingInput.value = "0";
        });
    }

    if (addCommentForm) {
        addCommentForm.addEventListener('submit', async function (event) {
            event.preventDefault();
            const submitButton = this.querySelector('button[type="submit"]');
            const ratingInput = document.getElementById('userRatingInputV3');
            const commentTextInput = document.getElementById('commentTextV3'); // Adapted ID
            const commenterNameInput = document.getElementById('commenterNameV3'); // Adapted ID

            const rating = ratingInput ? ratingInput.value : '0';
            const commentText = commentTextInput ? commentTextInput.value : '';
            const commenterName = (commenterNameInput && commenterNameInput.value) ? commenterNameInput.value : translate('visitor');

            if (commentText.trim() === '') {
                alert(translate('please_write_commit'));
                return;
            }
            // Optional: Check for rating if it's mandatory (original only checked text)
            // if (parseInt(rating) === 0) { alert('الرجاء تحديد تقييم.'); return; }

            toggleButtonLoading(submitButton, true);
            await new Promise(resolve => setTimeout(resolve, 900)); // Simulate network

            const newCommentData = {
                id: Date.now(),
                author: commenterName,
                avatar: `https://placehold.co/36x36/ccc/000?text=${commenterName.substring(0, 1).toUpperCase() || 'Z'}`,
                rating: parseInt(rating) || 0,
                text: commentText,
                date: 'الآن',
                parentId: null,
                replies: []
            };
            allComments.unshift(newCommentData); // Add to top of data

            // Refresh display
            if (commentListEl.querySelector('p.no-more-comments-v3[style*="display:block"]') || document.getElementById(initialCommentLoadingMessageId)) {
                commentListEl.innerHTML = ''; // Clear "no comments" or loading message
                commentsDisplayedCount = 0; // Reset counter
                loadComments(); // Reload comments to show the new one
            } else {
                commentListEl.prepend(createCommentElement(newCommentData, 0)); // Add to top of list
                commentsDisplayedCount++; // Increment displayed count as one is prepended
                // Ensure load more button visibility is re-evaluated
                const topLevelComments = allComments.filter(c => c.parentId === null);
                if (commentsDisplayedCount < topLevelComments.length && loadMoreCommentsBtn) {
                    loadMoreCommentsBtn.style.display = 'block';
                }
                if (noMoreCommentsMessage) noMoreCommentsMessage.style.display = 'none';
            }

            if (totalCommentCountTitleEl) totalCommentCountTitleEl.textContent = countAllComments(allComments);
            displayRatings(); // Update rating summary

            toggleButtonLoading(submitButton, false); // Restore button
            alert(translate('thank_you_for_your_submission'));
            this.reset(); // Reset form fields
            const formStarRatingContainer = document.getElementById('formStarRatingV3');
            if (formStarRatingContainer) resetStarsVisual(formStarRatingContainer); // Reset stars
            if (ratingInput) ratingInput.value = "0";
            this.classList.remove('active'); // Hide form
        });
    }

    // Initial Loads
    displayRatings(); // Display initial rating summary (based on demo data)
    loadComments();   // Load initial comments
}