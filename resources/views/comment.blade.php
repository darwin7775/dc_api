<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/mylogo_bgwhite.png" type="image/png">
    <title>My Portfolio - DC API</title>

    <!-- Bootstrap 5 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<style>
    .star-rating {
        direction: rtl;
        display: inline-flex;
        font-size: 1.8rem;
    }
    .star-rating input[type="radio"] {
        display: none;
    }
    .star-rating label {
        color: #ccc;
        cursor: pointer;
        transition: color 0.2s;
    }
    .star-rating input[type="radio"]:checked ~ label {
        color: #ffc107;
    }
    .star-rating label:hover,
    .star-rating label:hover ~ label {
        color: #ffc107;
    }
</style>
<body>
    <main class="py-4">
        <div class="container mt-4">
            <div class="card bg-white shadow-sm">
                <div class="px-4 pt-4">
                    <h2>Hi!</h2>
                    <p>
                        This extension of my portfolio comment section,
                        if you don't want to use the api provided.
                        Your comments are so much appreciated, and they will help to grow more.
                        Thank you
                    </p>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Leave a Comment</h5>

                    <!-- Comment Form -->
                    <form id="comment-form" autocomplete="off">
                        @csrf

                        <!-- Commentor name -->
                        <div class="mb-3">
                            <label for="commentor" class="form-label">Your Name</label>
                            <input type="text" id="commentor" name="commentor" class="form-control" placeholder="Enter your name" required>
                        </div>

                        <!-- Comment -->
                        <div class="mb-3">
                            <label for="comment" class="form-label">Comment</label>
                            <textarea id="comment" name="comment" class="form-control" rows="4" placeholder="Write your comment here..." required></textarea>
                        </div>

                        <!-- Star Rating -->
                        <div class="mb-3">
                            <label class="form-label">Rating:</label>
                            <div class="star-rating">
                                @for ($i = 5; $i >= 1; $i--)
                                    <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}">
                                    <label for="star{{ $i }}" title="{{ $i }} stars">&#9733;</label>
                                @endfor
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Post Comment</button>
                    </form>

                </div>
            </div>
        </div>

        <div class="container mt-5">
            <h4>Comments <span class="badge bg-primary rounded-pill"><i class="comment_count" id="comment_count"></i></span></h4>
            <div id="comments-list" class="mb-3">
                <p>Loading comments...</p>
            </div>
        </div>
    </main>

    <!-- Bootstrap 5 JS (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</body>

<script>
    $(document).ready(function () {
        $('#comment-form').on('submit', function (e) {
            e.preventDefault();

            const formData = {
                commentor: $('#commentor').val(),
                comment: $('#comment').val(),
                rating: $('input[name="rating"]:checked').val(),
                _token: '{{ csrf_token() }}'
            };

            $.ajax({
                url: 'http://127.0.0.1:8000/api/form_store',
                type: 'POST',
                data: formData,
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Comment Posted!',
                        text: 'Your comment has been submitted.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    // Reset form fields
                    $('#comment-form')[0].reset();

                    // Reset star rating (uncheck all)
                    $('input[name="rating"]').prop('checked', false);
                    fetch_comment();
                },
                error: function (xhr) {
                    let errors = xhr.responseJSON.errors;
                    let errorMsg = '';

                    for (let field in errors) {
                        errorMsg += `<li>${errors[field][0]}</li>`;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        html: errorHtml,
                        confirmButtonText: 'Got it'
                    });
                }
            });
        });

        fetch_comment();
        function fetch_comment(){
            $.ajax({
                url: 'http://127.0.0.1:8000/api/all_comments',
                type: 'GET',
                dataType: 'json',
                success: function (comments) {
                    const container = $('#comments-list');
                    container.empty();

                    if (comments.length === 0) {
                        container.html('<p>No comments yet.</p>');
                        return;
                    }

                    comments.forEach(function (comment) {
                        const rating = parseFloat(comment.rating ?? 0);
                        const fullStars = Math.floor(rating);
                        const hasHalfStar = (rating - fullStars) >= 0.5;
                        const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

                        let starsHtml = '';
                        for (let i = 0; i < fullStars; i++) starsHtml += '★';
                        if (hasHalfStar) starsHtml += '⯨'; // Approx half-star
                        for (let i = 0; i < emptyStars; i++) starsHtml += '☆';

                        const commentHtml = `
                            <div class="card mb-2">
                                <div class="card-body">
                                    <p class="mb-1 fs-6 fw-bold">${escapeHtml(comment.commentor)}</p>
                                    <p class="mb-1">${escapeHtml(comment.comment)}</p>
                                    <small class="text-warning fs-3">${starsHtml}</small>
                                </div>
                            </div>
                        `;
                        container.append(commentHtml);
                    });
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching comments:', error);
                    $('#comments-list').html('<p class="text-danger">Failed to load comments.</p>');
                }
            });

            // Prevent XSS: escape text
            function escapeHtml(text) {
                return $('<div>').text(text).html();
            }
            count_all_comments();
        }

        function count_all_comments(){
            $.ajax({
                url: 'http://127.0.0.1:8000/api/all_comments_count',
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    // If API returns a number directly
                    $('#comment_count').text(data);
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching comment count:', error);
                    $('#comments-list').html('<p class="text-danger">Failed to load comments.</p>');
                }
            });
        }

        setInterval(fetch_comment, 10000);
    });


</script>
</html>

