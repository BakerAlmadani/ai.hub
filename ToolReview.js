document.addEventListener("submit", function (event) {
    event.preventDefault();

    const UserName = document.getElementById("UserName").value.trim();
    const email = document.getElementById("Email").value.trim();
    const Comment = document.getElementById("Comment").value.trim();
    const Rating = document.getElementById("Rating").value;
    
    if (UserName === "") {
        alert("Please enter your username.");
        return;
    }

    if (UserName.length < 3) {
        alert("Username must be at least 3 characters.");
        return;
    }

    if (Comment.length > 300) {
        alert("Comment is too long.");
        return;
    }

    const action = event.submitter.value;

    fetch("tool.php" + window.location.search, {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            username: UserName,
            email: email,
            comments: Comment,
            rating: Rating,
            action: action
        })
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById("responseMessage").innerHTML = data;
        
        if(data.includes('success')) {
            setTimeout(() => window.location.reload(), 1500); 
        }
    })
    .catch(error => {
        console.error(error);
    });
});