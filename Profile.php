<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Profile</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>

</head>
<body>

  <div class="rectangle-1"></div>
  <!-- User Icon on Rectangle 1 -->
  <div class="rectangle-user-icon">
    <i class="fa-solid fa-circle-user"></i>
  </div>
  <div class="logout-button"> Logout </div>

  <!-- User Info -->
  <div class="profile-name">Name</div>
  <a href="#view-profile" class="view-profile">View Profile</a>
  <a href="#inbox" class="inbox">INBOX</a>
  <a href="#search" class="search">SEARCH</a>
  <a href="#community" class="community">COMMUNITY</a>


<style>

  html, body {
    margin: 0;
    padding: 0;
    overflow: hidden; 
    height: 100%;
  }

  .rectangle-1 {
    position: absolute;
    width: 850px;
    height: 1029px;
    left: 790px;
    top: -3px;
    background: #EEFF00;
    box-shadow: 0px 4px 4px #767676;
    border-radius: 10px;
    z-index: 30;
  }

  .rectangle-user-icon {
    position: absolute;
    top: 62px;       
    left: 820px;     
    z-index: 30;     
    font-size: 70px;
    color: #000000;
  }

  .view-profile {
    position: absolute;
    width: 614px;
    height: 51px;
    left: 925px;
    top: 106px;
    font-family: 'Inter';
    font-style: normal;
    font-weight: 400;
    font-size: 18px;
    line-height: 24px;
    color: #000000;
    z-index: 30;
    text-decoration: none;
  }

  .profile-name {
    position: absolute;
    width: 102px;
    height: 50px;
    left: 920px;
    top: 62px;
    font-family: 'Inter';
    font-style: normal;
    font-weight: 700;
    font-size: 32px;
    line-height: 39px;
    color: #000000;
    z-index: 30;
    white-space: nowrap;
  }

  .inbox {
    position: absolute;
    left: 830px;
    top: 190px;
    font-family: 'Inter';
    font-weight: 700;
    font-size: 40px;
    line-height: 39px;
    color: #000000;
    z-index: 30;
    text-decoration: none;
  }

  .search {
    position: absolute;
    left: 830px;
    top: 275px;
    font-family: 'Inter';
    font-weight: 700;
    font-size: 40px;
    line-height: 39px;
    color: #000000;
    z-index: 30;
    text-decoration: none;
  }

  .community {
    position: absolute;
    left: 830px;
    top: 360px;
    font-family: 'Inter';
    font-weight: 700;
    font-size: 40px;
    line-height: 39px;
    color: #000000;
    z-index: 30;
    text-decoration: none;
  }

  .logout-button {
    position: absolute;
    width: 500px;
    height: 60px;
    bottom: 60px;
    left: 930px; 
    background: F2FF3F;
    color: 000;
    font-size: 24px;
    font-weight: bold;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 25px;
    cursor: pointer;
    z-index: 30;
    box-shadow: 5px 4px 4px rgba(0, 0, 0, 0.25);
  }

  .logout-button:hover {
    background: #444; 
  }

</style>
</body>
</html>
