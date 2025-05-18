<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #1c1c1c;
      color: #fff;
    }

    .contact-wrapper {
      display: flex;
      min-height: 100vh;
    }

    .contact-info {
      background: linear-gradient(135deg, #000000, #2d2d2d);
      color: #ffcc00;
      padding: 40px 30px;
      width: 40%;
      display: flex;
      flex-direction: column;
      justify-content: center;
      font-size: 18px;
    }

    .contact-info h2 {
      font-size: 48px;
      font-weight: bold;
      margin-bottom: 40px;
      margin-left: 10px;
    }

    .contact-info p {
      font-size: 18px;
      margin-bottom: 40px;
      line-height: 1.5;
      margin-left: 10px;
      color: #f5f5f5;
    }

    .contact-info .info-item {
      font-size: 18px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      margin-left: 10px;
    }

    .contact-info .info-item i {
      font-size: 20px;
      margin-right: 15px;
      background: #ffcc00;
      color: #000;
      padding: 10px;
      border-radius: 50%;
    }

    .contact-info .social-icons i {
      font-size: 18px;
      background: #ffcc00;
      color: #000;
      padding: 10px;
      border-radius: 8px;
      margin-right: 10px;
      cursor: pointer;
      transition: background 0.3s;
      margin-left: 10px;
      margin-bottom: 80px;
    }

    .contact-info .social-icons i:hover {
      background: #e6b800;
    }

    .contact-form {
      background: #2b2b2b;
      width: 60%;
      padding: 60px 40px;
      color: #fff;
    }

    .contact-form h2 {
      font-weight: bold;
      margin-bottom: 30px;
      font-size: 32px;
      color: #ffcc00;
    }

    .form-control {
      border: none;
      background: #444;
      color: #fff;
      border-radius: 20px;
      padding: 15px 20px;
      margin-bottom: 20px;
      box-shadow: inset 0 1px 3px rgba(0,0,0,0.4);
    }

    .form-control::placeholder {
      color: #ccc;
    }

    .form-control:focus {
      box-shadow: 0 0 5px #ffcc00;
      background: #555;
    }

    .send-btn {
      background: #ffcc00;
      border: none;
      border-radius: 30px;
      padding: 15px 40px;
      color: #000;
      font-weight: bold;
      transition: background 0.3s ease;
    }

    .send-btn:hover {
      background: #e6b800;
    }

    @media (max-width: 768px) {
      .contact-wrapper {
        flex-direction: column;
      }

      .contact-info, .contact-form {
        width: 100%;
      }
    }
  </style>
</head>
<body>

<div class="contact-wrapper">
  <div class="contact-info">
    <h2>Contact Information</h2>
    <p>Fill up the form and our Team will get back to you within 24 hours</p>
    <div class="info-item"><i class="fas fa-phone"></i> Phone: +1235 2355 98</div>
    <div class="info-item"><i class="fas fa-paper-plane"></i> Email: info@yoursite.com</div>
    <div class="info-item"><i class="fas fa-globe"></i> Website: yoursite.com</div>
    <div class="social-icons mt-4">
      <i class="fab fa-facebook-f"></i>
      <i class="fab fa-twitter"></i>
      <i class="fab fa-linkedin-in"></i>
    </div>
  </div>

  <div class="contact-form">
    <h2>Send us a message</h2>
    <!-- Back Button -->
    <button onclick="history.back()" class="send-btn mt-3 mb-3">Back</button>
    <form>
      <div class="row">
        <div class="col-md-6">
          <input type="text" class="form-control" placeholder="First Name" required>
        </div>
        <div class="col-md-6">
          <input type="text" class="form-control" placeholder="Last Name" required>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <input type="email" class="form-control" placeholder="Mail" required>
        </div>
        <div class="col-md-6">
          <input type="text" class="form-control" placeholder="Phone" required>
        </div>
      </div>
      <textarea class="form-control" rows="5" placeholder="Write your message" required></textarea>
      <button type="submit" class="send-btn mt-3">Send Message</button>
    </form>
  </div>
</div>

</body>
</html>