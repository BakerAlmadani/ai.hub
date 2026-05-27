document.addEventListener('submit', function (event) {
    event.preventDefault(); 

    const Email = document.getElementById('email').value.trim();
    const Rating = document.getElementById('rating').value;
    const Comment = document.getElementById('comment').value.trim();
    const Issue = document.getElementById('issues').value.trim();
    const action = event.submitter.value;


    if (Comment.length > 300) {
        alert("Comment is too long.");
        event.preventDefault();
        return;
    }

    if (Issue.length > 300){
        alert("Issue is too long.");
        event.preventDefault();
        return;
    }

    fetch("Reviews.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            email: Email,
            comments: Comment,
            rating: Rating,
            issues: Issue,
            action: action
        })
    })
    .then(response => response.text())
    .then(data => {
        // Display the PHP response in the message div
        document.getElementById("responseMessage").innerHTML = data;
    })
    .catch(error => {
        console.error(error);
    });
});