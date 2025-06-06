// Open Modals
document.getElementById('open-modals').addEventListener('click', function() {
    document.getElementById('upload-modals').style.display = 'flex';
});

// Close Modals
document.querySelector('.close-modals').addEventListener('click', function() {
    document.getElementById('upload-modals').style.display = 'none';
});

// Trigger File Upload
document.getElementById('choose-file-btn').addEventListener('click', function() {
    document.getElementById('photo-upload').click();
});

// Show Selected File Name
document.getElementById('photo-upload').addEventListener('change', function() {
    const fileName = this.files[0] ? this.files[0].name : "No file chosen";
    document.getElementById('selected-file-name').textContent = fileName;
});

// Upload Image
document.getElementById('upload-btn').addEventListener('click', function() {
    const fileInput = document.getElementById('photo-upload');
    const file = fileInput.files[0];
    const name = document.getElementById('product-name').value.trim();
    const doc = document.getElementById('product-doc').value.trim();

    if (!file) {
        alert("Please select a file to upload.");
        return;
    }

    const reader = new FileReader();
    reader.onload = function(event) {
        const imageHtml = document.createElement("div");
        imageHtml.classList.add("documented-photo");
        imageHtml.innerHTML = `
            <img src="${event.target.result}" alt="Uploaded Photo">
            <div class="product-info">
                <strong>${name || "No Name"}</strong>
                <p class="product-description">${doc || "No Description"}</p>
                <button class="read-more-btn">Read More</button>
            </div>
            <div class="review-section">
                <textarea class="review-input" placeholder="Write a review..."></textarea>
                <button class="review-btn">Submit Review</button>
                <div class="reviews"></div>
            </div>
        `;

        // Append image container to the photo documentation section
        document.getElementById('photo-documentation').appendChild(imageHtml);

        // Review submission
        imageHtml.querySelector('.review-btn').addEventListener('click', function() {
            const reviewInput = imageHtml.querySelector('.review-input');
            const reviewText = reviewInput.value.trim();

            if (reviewText !== "") {
                const reviewDisplay = document.createElement("p");
                reviewDisplay.textContent = `⭐ ${reviewText}`;
                imageHtml.querySelector('.reviews').appendChild(reviewDisplay);
                reviewInput.value = ""; // Clear input after submitting
            } else {
                alert("Please enter a review before submitting.");
            }
        });

        // Read More button functionality
        const readMoreBtn = imageHtml.querySelector('.read-more-btn');
        const description = imageHtml.querySelector('.product-description');

        if (doc.length > 100) { 
            description.style.maxHeight = "50px";
            description.style.overflow = "hidden";
            description.style.textOverflow = "ellipsis";
            description.style.whiteSpace = "nowrap";

            readMoreBtn.addEventListener('click', function() {
                if (description.style.whiteSpace === "nowrap") {
                    description.style.whiteSpace = "normal";
                    description.style.overflow = "visible";
                    readMoreBtn.textContent = "Read Less";
                } else {
                    description.style.whiteSpace = "nowrap";
                    description.style.overflow = "hidden";
                    readMoreBtn.textContent = "Read More";
                }
            });
        } else {
            readMoreBtn.style.display = "none"; // Hide Read More if the text is short
        }
    };

    reader.readAsDataURL(file);
    document.getElementById('upload-modals').style.display = 'none';
});
