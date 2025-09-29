document.addEventListener('DOMContentLoaded', () => {
	const commentForm = document.getElementById('comment-form');

	if (commentForm) {
		const commentText = document.getElementById('comment-text');
		const commentsList = document.getElementById('comments-list');

		commentForm.addEventListener('submit', (event) => {
			event.preventDefault();

			const formData = new FormData(commentForm);

			fetch('../../controller/post_comment_action.php', {
				method: 'POST',
				body: formData
			})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						const noCommentsMessage = document.getElementById('no-comments-message');

						const newComment = document.createElement('div');
						newComment.classList.add('comment');

						const authorStrong = document.createElement('strong');
						authorStrong.textContent = data.author;

						const dateSpan = document.createElement('span');
						dateSpan.classList.add('comment-date');
						dateSpan.textContent = data.created_at;

						const commentP = document.createElement('p');
						commentP.innerHTML = data.comment_text.replace(/\n/g, '<br>');

						newComment.appendChild(authorStrong);
						newComment.appendChild(dateSpan);
						newComment.appendChild(commentP);

						commentsList.prepend(newComment);

						commentText.value = '';

						if (noCommentsMessage) {
							noCommentsMessage.remove();
						}
					} else {
						alert('Could not post comment: ' + data.error);
					}
				})
				.catch(error => {
					console.error('Error submitting comment:', error);
					alert('An unexpected error occurred. Please try again.');
				});
		});
	}
});

