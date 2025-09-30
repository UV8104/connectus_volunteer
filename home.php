<?php
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VM System - Volunteer Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: #333;
            min-height: 100vh;
        }

        /* Disable text selection */
        body {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        /* Navigation Bar -- MODIFIED */
        nav {
            background: #ffffff; /* Changed to solid white */
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        /* Logo Styling -- MODIFIED */
        .logo img {
            height: 40px; /* Controls the height of your logo image */
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .nav-links {
            display: flex;
            gap: 2rem;
        }

        .nav-links a {
            text-decoration: none;
            color: #444;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .nav-links a:hover {
            background: linear-gradient(90deg, #6a11cb, #2575fc);
            color: white;
            transform: translateY(-2px);
        }

        /* Home Section */
        .home-section {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 6rem 2rem 3rem;
            text-align: center;
            color: white;
        }

        .banner {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 3rem;
            border-radius: 20px;
            max-width: 800px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            margin-bottom: 2rem;
        }

        .banner h1 {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(90deg, #ff9a9e, #fad0c4);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .banner p {
            font-size: 1.2rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .cta-button {
            background: linear-gradient(90deg, #6a11cb, #2575fc);
            color: white;
            border: none;
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .cta-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }

        /* Additional Sections */
        .section {
            padding: 5rem 2rem;
            background: white;
            margin: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .section h2 {
            font-size: 2.5rem;
            margin-bottom: 2rem;
            text-align: center;
            background: linear-gradient(90deg, #6a11cb, #2575fc);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .features {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            justify-content: center;
        }

        .feature-card {
            background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
            padding: 2rem;
            border-radius: 15px;
            flex: 1 1 300px;
            max-width: 350px;
            text-align: center;
            color: white;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            cursor: pointer;
        }

        .feature-card:nth-child(2) {
            background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
        }

        .feature-card:nth-child(3) {
            background: linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%);
        }

        .feature-card:hover {
            transform: translateY(-10px);
        }

        .feature-card i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        /* Testimonials */
        .testimonials {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 5rem 2rem;
            text-align: center;
        }

        .testimonial-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            justify-content: center;
            margin-top: 3rem;
        }

        .testimonial-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 15px;
            flex: 1 1 300px;
            max-width: 350px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 1rem;
            font-size: 2.5rem;
            font-weight: bold;
            color: white;
        }

        /* Feature Modal */
        .feature-modal-container {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            justify-content: center;
            align-items: center;
            z-index: 2000;
        }

        .feature-modal {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 3rem;
            border-radius: 20px;
            width: 90%;
            max-width: 700px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            animation: popIn 0.5s ease-out;
            position: relative;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .feature-content {
            margin-top: 1.5rem;
        }
        
        .feature-content h3 {
            margin: 1.5rem 0 0.5rem;
            color: #ff9a9e;
        }
        
        .feature-content p {
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        @keyframes popIn {
            0% { transform: scale(0.8); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        .close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #ff4757;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            font-weight: bold;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }

        /* Footer */
        footer {
            background: rgba(0, 0, 0, 0.8);
            color: white;
            text-align: center;
            padding: 2rem;
            margin-top: 3rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                padding: 1rem;
            }

            .nav-links {
                margin-top: 1rem;
                gap: 1rem;
            }

            .banner h1 {
                font-size: 2.5rem;
            }

            .banner {
                padding: 2rem;
            }
            
            .feature-modal {
                padding: 2rem;
            }

            .section {
                margin: 1rem;
                padding: 3rem 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .banner h1 {
                font-size: 2rem;
            }

            .banner p {
                font-size: 1rem;
            }

            .cta-button {
                padding: 0.8rem 1.8rem;
            }
            
            .feature-modal {
                padding: 1.5rem;
            }
            
            .feature-card, .testimonial-card {
                flex: 1 1 100%;
            }
        }
    </style>
</head>
<body>
    <nav>
        <div class="logo">
            <img src="logoo.jpg" alt="VM System Logo">
        </div>
        <div class="nav-links">
            <a href="#" class="active">Home</a>
            <a href="#features">Features</a>
            <a href="#testimonials">Testimonials</a>
            <a href="login.php">Login</a>

        </div>
    </nav>

    <section class="home-section">
        <div class="banner">
            <h1>Welcome to VM System</h1>
            <p>Our Volunteer Management System helps organizations coordinate, schedule, and communicate with volunteers more effectively than ever before.</p>
            
        </div>
        <div style="color: white; margin-top: 2rem;">
            <p>Scroll down to learn more</p>
            <i class="fas fa-chevron-down" style="font-size: 2rem; margin-top: 1rem; animation: bounce 2s infinite;"></i>
        </div>
    </section>

    <section id="features" class="section">
        <h2>Powerful Features</h2>
        <div class="features">
            <div class="feature-card" data-feature="scheduling">
                <i class="fas fa-calendar-alt"></i>
                <h3>Easy Scheduling</h3>
                <p>Create and manage volunteer schedules with our intuitive drag-and-drop calendar interface.</p>
            </div>
            <div class="feature-card" data-feature="communication">
                <i class="fas fa-bullhorn"></i>
                <h3>Communication Tools</h3>
                <p>Send announcements, reminders, and updates to your volunteer team instantly.</p>
            </div>
        </div>
    </section>

    <section id="testimonials" class="testimonials">
        <h2>What Our Users Say</h2>
        <div class="testimonial-cards">
            <div class="testimonial-card">
                <div class="user-avatar">M</div>
                <h3>Mahesh</h3>
                <p>"Very easy to use! I can see all upcoming events clearly and register in just one click. It saves me a lot of time"</p>
            </div>
            <div class="testimonial-card">
                <div class="user-avatar">A</div>
                <h3>Amit</h3>
                <p>"The reminders and notifications are very helpful. I never miss any event now!"</p>
            </div>
            <div class="testimonial-card">
                <div class="user-avatar">A</div>
                <h3>Arjun</h3>
                <p>"The dashboard is colorful and simple. Even first-time users can understand everything quickly."</p>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; 2025 VM System - Volunteer Management Solutions. All rights reserved.</p>
    </footer>

    <div class="feature-modal-container" id="feature-modal-container">
        <div class="feature-modal" id="feature-modal">
            <div class="close-btn" id="close-feature">×</div>
            <h2 id="feature-title">Feature Details</h2>
            <div class="feature-content" id="feature-content">
                </div>
        </div>
    </div>

    <script>
        // Disable right-click context menu
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        // Feature card click handling
        const featureCards = document.querySelectorAll('.feature-card');
        const featureModal = document.getElementById('feature-modal-container');
        const featureTitle = document.getElementById('feature-title');
        const featureContent = document.getElementById('feature-content');
        const closeFeature = document.getElementById('close-feature');
        
        // Feature content
        const featureData = {
            scheduling: {
                title: "Easy Scheduling",
                content: `
                    <p>Here's how the team supports volunteers through Easy Scheduling:</p>
                    
                    <h3>🔹 1. Clear Event Calendar</h3>
                    <p>Volunteers can see upcoming events with date, time, and location on a calendar or list view.</p>
                    <p>Example: "Tree Plantation Drive – 25th Aug, 9:00 AM, City Park."</p>
                    <p>This helps volunteers plan their personal time easily.</p>
                    
                    <h3>🔹 2. Shift Management</h3>
                    <p>Admin can create shifts (Morning, Afternoon, Evening) for events.</p>
                    <p>Volunteers can pick the shift that suits their availability.</p>
                    <p>Support: No clashes with personal schedules → reduces stress.</p>
                    
                    <h3>🔹 3. Auto-Scheduling / Smart Assignment</h3>
                    <p>The system can automatically assign volunteers to tasks based on their skills, availability, and preferences.</p>
                    <p>Example: A volunteer with first-aid training is auto-assigned to the "Medical Help Desk."</p>
                    <p>Support: Volunteers get work matching their interests/skills → more motivation.</p>
                    
                    <h3>🔹 4. Conflict-Free Scheduling</h3>
                    <p>The system avoids double-booking of volunteers.</p>
                    <p>If a volunteer is already assigned to one event, they won't be scheduled for another at the same time.</p>
                    <p>Support: Prevents confusion and overwork.</p>
                    
                    <h3>🔹 5. Reminders & Notifications</h3>
                    <p>Volunteers get reminders about their assigned shifts/events.</p>
                    <p>Example: "Reminder: You are scheduled for Registration Desk at 8:00 AM tomorrow."</p>
                    <p>Support: Helps them prepare in advance.</p>
                    
                                
                    <p><strong>✅ In short:</strong><br>
                    Easy Scheduling in VMS supports volunteers by giving them a clear, flexible, and well-organized plan of their duties, ensuring they know when, where, and what they are supposed to do without confusion.</p>
                `
            },
            communication: {
                title: "Communication Tools",
                content: `
                    <p>Here's how your VMS team can support volunteers through communication tools:</p>
                    
                    <h3>🔹 1. Announcements / Notifications</h3>
                    <p>The admin can send updates about new events, schedule changes, or important instructions.</p>
                    <p>Example: "Event timing changed from 9:00 AM to 10:30 AM."</p>
                    <p>Volunteers instantly get notified via the system dashboard, email, or SMS.</p>
                    
                    <h3>🔹 2. Message/Chat System</h3>
                    <p>A built-in chat or message feature allows two-way communication.</p>
                    <p>Volunteers can ask questions, and the admin can reply directly.</p>
                    <p>Example: Volunteer: "Where should I report for tomorrow's event?"<br>
                    Admin: "Please reach Hall No. 2 at 8:30 AM."</p>
                    
                    <h3>🔹 3. Support / Help Desk</h3>
                    <p>A "Help" or "Contact Support" section where volunteers can submit queries/issues.</p>
                    <p>Example: "I am unable to enroll in the Blood Donation Camp." → Admin resolves it.</p>
                    
                    <h3>🔹 4. Feedback System</h3>
                    <p>After events, volunteers can share feedback about their experience.</p>
                    <p>The admin can use this to improve future events and also motivate volunteers.</p>
                    
                    <h3>🔹 5. Event Reminders & Alerts</h3>
                    <p>Automatic reminders before an event starts.</p>
                    <p>Example: "Reminder: You are scheduled for the Clean-Up Drive tomorrow at 7 AM."</p>
                    <p>Helps volunteers stay organized and reduces missed participation.</p>
                    
                                
                    
                    <p><strong>✅ In short:</strong><br>
                    The communication tool in a Volunteer Management System makes sure the team supports volunteers by keeping them informed, answering their questions, providing reminders, and collecting feedback — just like a bridge between management and volunteers.</p>
                `
            }
        };
        
        // Add click event to feature cards
        featureCards.forEach(card => {
            card.addEventListener('click', function() {
                const featureType = this.getAttribute('data-feature');
                const feature = featureData[featureType];
                
                featureTitle.textContent = feature.title;
                featureContent.innerHTML = feature.content;
                
                featureModal.style.display = 'flex';
            });
        });
        
        // Close feature modal
        closeFeature.addEventListener('click', function() {
            featureModal.style.display = 'none';
        });

        // Close the feature modal if user clicks outside of it
        window.addEventListener('click', function(event) {
            const featureModal = document.getElementById('feature-modal-container');
            
            if (event.target === featureModal) {
                featureModal.style.display = 'none';
            }
        });

        // Add bounce animation for the chevron
        const style = document.createElement('style');
        style.textContent = `
            @keyframes bounce {
                0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
                40% {transform: translateY(-20px);}
                60% {transform: translateY(-10px);}
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>