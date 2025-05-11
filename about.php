<?php include 'menu.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ABOUT US</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@400;700&display=swap" rel="stylesheet">

  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(to right, #fdfd96, #fff);
      color: #f2f2f2;
    }

    h2 {
      font-size: 2.5rem;
      font-family: 'Roboto Slab';
      background: #000000;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 40px;
    }

    .service-card {
      background-color: #1a1a1a;
      border-radius: 12px;
      padding: 30px 30px;
      border: 2px solid transparent;
      transition: transform 0.3s ease, background-color 0.3s ease, box-shadow 0.3s ease;
      text-align: center;
      cursor: pointer;
      height: 100%;
    }

    .service-card:hover {
      border-color: #ffd54f;
      box-shadow: 0 0 15px rgba(255, 213, 79, 0.5);
      transform: scale(1.05); /* Slight scale effect */
      background-color: #333; /* Slight darkening of the background */
    }

    .service-card i {
      font-size: 40px;
      color: #fdd835;
      margin-bottom: 15px;
      transition: color 0.3s;
    }

    .service-card:hover i {
      color: #ffeb3b;
    }

    .service-title {
      font-weight: bold;
      font-size: 1.2rem;
      margin-bottom: 10px;
      color: #fdd835;
    }

    .service-text {
      font-size: 0.95rem;
      color: #d6d6d6;
    }
  </style>
</head>
<body>
  <section class="py-5 text-center">
    <div class="container">
      <h2>SKILLSWAP</h2>
      <div class="row g-4 justify-content-center">
        <!-- First 3 cards -->
        <div class="col-md-6 col-lg-4">
          <div class="service-card h-100">
            <i class="fas fa-signal"></i>
            <div class="service-title">Skill Exchange System</div>
            <div class="service-text">SKILLSWAP is built around the concept of peer-to-peer skill sharing through a modern barter system. Users can offer their own skills—such as graphic design, cooking, coding, or photography—in exchange for a skill they want to learn. This structure removes the need for money in most learning interactions and creates a mutually beneficial learning environment where every user is both a student and a teacher.</div>
          </div>
        </div>
        <div class="col-md-6 col-lg-4">
          <div class="service-card h-100">
            <i class="fas fa-layer-group"></i>
            <div class="service-title">Free Learning Opportunities</div>
            <div class="service-text">One of the core goals of SKILLSWAP is to provide accessible education by enabling users to learn new skills without financial cost. By trading time and knowledge instead of money, the platform ensures that anyone, regardless of their economic status, can continue developing their talents and gaining practical knowledge in a wide range of fields.</div>
          </div>
        </div>
        <div class="col-md-6 col-lg-4">
          <div class="service-card h-100">
            <i class="fas fa-user-friends"></i>
            <div class="service-title">Mentorship</div>
            <div class="service-text">SKILLSWAP offers a mentorship feature for users who want structured, expert-led learning. Experienced users can list specific skills they mentor in, and others can pay for one-on-one sessions or guidance. This feature adds a professional layer to the platform and helps mentors earn from their expertise while helping others grow.</div>
          </div>
        </div>

        <!-- Next 3 cards -->
        <div class="col-md-6 col-lg-4">
          <div class="service-card h-100">
            <i class="fas fa-cogs"></i>
            <div class="service-title">Community-Based Interaction</div>
            <div class="service-text">To enhance collaboration and engagement, SKILLSWAP includes community spaces where users can connect based on the skills they offer or want to learn. These communities act as discussion groups or forums where members can share resources, post learning tips, ask questions, and support one another. This encourages long-term relationships and peer learning beyond individual skill swaps.</div>
          </div>
        </div>
        <div class="col-md-6 col-lg-4">
          <div class="service-card h-100">
            <i class="fas fa-chart-line"></i>
            <div class="service-title">User-Controlled Bartering</div>
            <div class="service-text">Each user has full autonomy over the exchange process. When a barter request is received, the user can review the details—including the skill offered, the skill requested, and the user's profile—before choosing to accept or decline. This system gives users the freedom to manage their own learning journey and ensures that exchanges only happen when both parties agree.</div>
          </div>
        </div>
        <div class="col-md-6 col-lg-4">
          <div class="service-card h-100">
            <i class="fas fa-mobile-alt"></i>
            <div class="service-title">Empowering Growth Through Connection</div>
            <div class="service-text">At its core, SKILLSWAP is designed to foster personal and collective growth. By creating a space where people can share their abilities, gain new knowledge, and connect with others who have similar goals, the platform encourages users to build confidence, discover new opportunities, and support one another in their learning journeys.</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>