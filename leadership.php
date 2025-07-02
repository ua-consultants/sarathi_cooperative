<?php
$pdo = new PDO(
    "mysql:host=localhost;dbname=u828878874_sarathi_db",
    "u828878874_sarathi_new",
    "#Sarathi@2025"
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leadership - Sarathi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <link rel="icon" href="img/logo-favi-icon.png">

    <style>
        .leadership-section {
            padding: 4rem 0;
            background: #f8f9fa;
            margin-bottom: 55px;
        }
        .main-heading {
            text-align: center;
            margin-top: 38px;
            margin-bottom: 1rem;
            font-size: 2.5rem;
            /*font-family: 'Dancing Script', cursive;*/
        }
        .intro-text {
            text-align: center;
            max-width: 800px;
            margin: 0 auto 4rem;
            color: #666;
            font-size: 1.5rem;
            border: none;
            border-radius: 8px;
        }
        .board-member {
            margin-bottom: 4rem;
            display: flex;
            align-items: center;
            gap: 2rem;
        }
        .board-member:nth-child(even) {
            flex-direction: row-reverse;
        }
        .member-image {
            flex: 0 0 300px;
            height: 400px;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .member-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .member-info {
            flex: 1;
        }
        .member-name {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
        .member-position {
            color: #666;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }
        .member-bio {
            line-height: 1.6;
        }
        .rules-button {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: #ffd700;
            color: #000;
            border: none;
            padding: 1rem 2rem;
            border-radius: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: all 0.3s;
        }
        .rules-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.15);
        }
        .rules-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
        }
        .rules-content {
            background: white;
            width: 90%;
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            border-radius: 8px;
            position: relative;
        }
        .close-button {
            position: absolute;
            right: 1rem;
            top: 1rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .board-member {
                flex-direction: column !important;
                text-align: center;
            }
            .member-image {
                flex: 0 0 auto;
                width: 100%;
                max-width: 300px;
                margin: 0 auto;
            }
        }
    </style>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "Sarathi Cooperative",
      "alternateName": [
        "Sarathi Research Consulting and Management Services",
        "Sarathi Research Services",
        "Sarathi Consulting Services",
        "Sarathi Marketing Services",
        "Sarathi Services",
        "Sarathi Research and Marketing Services",
        "Sarathi Research and Consulting Services",
        "Sarathi Consulting and Marketing Services",
        "Research and Consulting Services",
        "Marketing Services",
        "Marketing and Consulting Services",
        "Sarathi Consultants",
        "Sarathi Consultancy"
      ],
      "url": "https://www.sarathicooperative.com"
    }
</script>

</head>
<body>
    <?php include 'header.php'; ?>

    <div class="leadership-section">
        <div class="container">
            <h1 class="main-heading">Sarathi Board</h1>
            <p class="intro-text">
                The board is elected for 5 years, elections are held in the month of march and 
                fully subscribed members are enlisted for participation per rules as attached.
            </p>

            <div class="board-members">
                <?php
                $stmt = $pdo->query("SELECT bm.*, m.first_name, m.last_name, m.profile_image 
                                    FROM board_members bm 
                                    JOIN members m ON bm.member_id = m.id 
                                    WHERE bm.status = 'active' 
                                    ORDER BY bm.position_order");
                while($member = $stmt->fetch(PDO::FETCH_ASSOC)):
                ?>
                <div class="board-member">
                    <div class="member-image">
                        <img src="<?php echo htmlspecialchars($member['profile_image']); ?>" 
                             alt="<?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?>">
                    </div>
                    <div class="member-info">
                        <h2 class="member-name">
                            <?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?>
                        </h2>
                        <div class="member-position">
                            <?php echo htmlspecialchars($member['position']); ?>
                        </div>
                        <div class="member-bio">
                            <?php echo nl2br(htmlspecialchars($member['bio'])); ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <!--<button class="rules-button" onclick="showRules()">Board Rules</button>-->

    <!--<div class="rules-modal" id="rulesModal">-->
    <!--    <div class="rules-content">-->
    <!--        <button class="close-button" onclick="hideRules()">&times;</button>-->
    <!--        <h3>Board Member Rules</h3>-->
    <!--        <div class="rules-text">-->
                <!-- Add your rules content here -->
    <!--            <p>1. Board members are elected for a term of 5 years.</p>-->
    <!--            <p>2. Elections are held in March.</p>-->
    <!--            <p>3. Only fully subscribed members are eligible to participate.</p>-->
                <!-- Add more rules as needed -->
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->

    <?php include 'footer.php'; ?>

    <script>
    function showRules() {
        document.getElementById('rulesModal').style.display = 'block';
    }

    function hideRules() {
        document.getElementById('rulesModal').style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target == document.getElementById('rulesModal')) {
            hideRules();
        }
    }
    </script>
</body>
</html>