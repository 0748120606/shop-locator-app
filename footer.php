<div class="contact">
        <form>
            <label for="name">Name *</label>
            <input type="text" id="name" name="name" placeholder="Moses Paul" required>

            <label for="email">Email address *</label>
            <input type="email" id="email" name="email" placeholder="email@website.com" required>

            <label for="phone">Phone number *</label>
            <input type="tel" id="phone" name="phone" placeholder="555-555-5555" required>

            <label for="message">Message</label>
            <textarea id="message" name="message" rows="4"></textarea>

            <div class="checkbox-container">
                    <input type="checkbox" id="consent" name="consent" required>
                    <label for="consent">I allow this website to store my submission so they can respond to my inquiry. *</label>
            </div>
            <button type="submit">Submit</button>
        </form>
    </div>

    <div class="container hours">
        <h2>Get in touch</h2><br>
        <p>Email: <a href="mailto:maingimoses20@gmail.com" style="text-decoration: underline;">maingimoses20@gmail.com</a></p><br>
        <h3>Hours</h3>
        <table align="center">
            <tr><td>Monday</td><td>9:00am – 10:00pm</td></tr>
            <tr><td>Tuesday</td><td>9:00am – 10:00pm</td></tr>
            <tr><td>Wednesday</td><td>9:00am – 10:00pm</td></tr>
            <tr><td>Thursday</td><td>9:00am – 10:00pm</td></tr>
            <tr><td>Friday</td><td>9:00am – 10:00pm</td></tr>
            <tr><td>Saturday</td><td>9:00am – 6:00pm</td></tr>
            <tr><td>Sunday</td><td>9:00am – 12:00pm</td></tr>
        </table>
    </div>
</div>
    <div class="footer">&copy; 2025 Shop Locator. All Rights Reserved.</div>
</body>
<script>
    document.querySelectorAll('.nav-links a').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault(); // Prevent instant jump

        const targetId = this.getAttribute('href').substring(1); // Remove #
        const targetSection = document.getElementById(targetId);

        if (targetSection) {
            let targetPosition = targetSection.offsetTop - 50; // Adjust for navbar height
            let startPosition = window.pageYOffset;
            let distance = targetPosition - startPosition;
            let duration = 3000; // Adjust duration 
            let startTime = null;

            function animation(currentTime) {
                if (startTime === null) startTime = currentTime;
                let timeElapsed = currentTime - startTime;
                let run = ease(timeElapsed, startPosition, distance, duration);
                window.scrollTo(0, run);
                if (timeElapsed < duration) requestAnimationFrame(animation);
            }

            function ease(t, b, c, d) {
                t /= d / 2;
                if (t < 1) return c / 2 * t * t + b;
                t--;
                return -c / 2 * (t * (t - 2) - 1) + b;
            }

            requestAnimationFrame(animation);
        }
    });
});

</script>

</html>
