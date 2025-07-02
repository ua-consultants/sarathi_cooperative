<?php
// Database connection setup
$host = 'localhost';  // Your database host 
$dbname = 'u828878874_sarathi_db';  // Your database name 
$username = 'u828878874_sarathi_new';  // Your database username 
$password = '#Sarathi@2025';  // Your database password 

try { 
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password); 
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

    // Fetch active members
    $query = "SELECT * FROM members WHERE status = 'active'";
    $stmt = $pdo->query($query);
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch filter options
    $expertise_query = "SELECT DISTINCT area_of_expertise FROM members WHERE status = 'active'";
    $states_query = "SELECT DISTINCT state FROM members WHERE status = 'active'";

    $expertise_stmt = $pdo->query($expertise_query);
    $states_stmt = $pdo->query($states_query);

    $expertise_result = $expertise_stmt->fetchAll(PDO::FETCH_ASSOC);
    $states_result = $states_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { 
    die("Connection failed: " . $e->getMessage()); 
} 

// Function to get the correct image URL
function getProfileImageUrl($profileImage) {
    $baseUrl = 'https://sarathicooperative.org'; // Use the correct domain
    $uploadsPath = '/admin/uploads/members/';
    $defaultImage = '/img/avatar.png'; // Default fallback image
    
    if (!empty($profileImage) && $profileImage !== null) {
        // Check if the profile_image already contains the full path
        if (strpos($profileImage, 'admin/uploads/members/') !== false) {
            // If it already contains the path, just add the base URL
            return $baseUrl . '/' . ltrim($profileImage, '/');
        } else {
            // If it's just the filename, add the full path
            return $baseUrl . $uploadsPath . htmlspecialchars($profileImage);
        }
    } else {
        return $baseUrl . $defaultImage;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>We Sarathians - Sarathi Cooperative</title>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="img/logo-favi-icon.png">
    <style>
        .weSarathians {
            padding: 0;
            min-height: 100vh;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .heading {
            margin-top: 120px;
            padding-top: 40px;
            font-size: 3rem;
            color: #002147;
            margin-bottom: 2rem;
            text-align: center;
            position: relative;
        }

        .heading::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background-color: #ffd700;
            margin: 1rem auto;
        }

        .content {
            display: flex;
            gap: 2rem;
            margin-top: 2rem;
        }

        .filterBox {
            width: 300px;
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            height: fit-content;
        }

        .filterBox h3 {
            margin-top: 0;
            margin-bottom: 1rem;
            color: #002147;
            font-size: 1.2rem;
        }

        .searchInput,
        .filterSelect {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            box-sizing: border-box;
        }

        .searchInput {
            background-color: #fff;
            border: 2px solid #ddd;
            transition: border-color 0.3s ease;
        }

        .searchInput:focus {
            border-color: #002147;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 33, 71, 0.3);
        }

        .filterSelect {
            background-color: #fff;
            cursor: pointer;
        }

        .clearFilters {
            width: 100%;
            padding: 0.5rem;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            color: #666;
            transition: all 0.3s ease;
        }

        .clearFilters:hover {
            background: #e9ecef;
            color: #002147;
        }

        .membersGrid {
            flex: 1;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            padding-bottom: 2rem;
        }

        .profileCard {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .profileCard:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .profileImage {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            overflow: hidden;
            margin-bottom: 1rem;
            border: 3px solid #ffd700;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }

        .profileImage img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /*transition: opacity 0.3s ease;*/
        }

        .profileImage img.loading {
            /*opacity: 0.5;*/
        }

        .profileCard h3 {
            margin: 0.5rem 0;
            color: #333;
            text-align: center;
        }

        .expertise {
            color: #666;
            margin-bottom: 1rem;
            text-align: center;
        }

        .connectButton {
            background: #0a2b4f;
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .connectButton:hover {
            background: #ffd700;
            color: #0a2b4f;
        }

        .knowMore {
            width: 100%;
            text-align: center;
            padding: 0.5rem;
            background: #f5f5f5;
            margin-top: 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .knowMore:hover {
            background: #eee;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modalContent {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        }

        .modalHeader {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .modalHeader img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ffd700;
        }

        .modalHeader h2 {
            margin: 0;
            color: #002147;
        }

        .modalHeader .cursive {
            color: #666;
            font-style: italic;
            margin: 0;
        }

        .modalBody section {
            margin-bottom: 1.5rem;
        }

        .modalBody h3 {
            color: #002147;
            margin-bottom: 0.5rem;
        }

        .closeButton {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .noResults {
            text-align: center;
            padding: 2rem;
            color: #666;
            font-size: 1.1rem;
        }

        .resultsCount {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            text-align: center;
        }

        @media (max-width: 1024px) {
            .content {
                flex-direction: column;
            }

            .filterBox {
                width: 100%;
            }

            .membersGrid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }

            .heading {
                font-size: 2.5rem;
                margin-top: 100px;
            }

            .membersGrid {
                grid-template-columns: 1fr;
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

    <div class="weSarathians">
        <div class="container">
            <h1 class="heading" alt="Sarathi Research Consulting and Management Services">We Sarathians</h1>
            
            <div class="content">
                <div class="filterBox">
                    <h3><i class="fas fa-filter"></i> Find Sarathians</h3>
                    
                    <select class="filterSelect" id="expertiseFilter">
                        <option value="">All Areas of Expertise</option>
                        <?php foreach ($expertise_result as $row): ?>
                            <option value="<?php echo htmlspecialchars($row['area_of_expertise']); ?>">
                                <?php echo htmlspecialchars($row['area_of_expertise']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select class="filterSelect" id="stateFilter">
                        <option value="">All States</option>
                        <?php foreach ($states_result as $row): ?>
                            <option value="<?php echo htmlspecialchars($row['state']); ?>">
                                <?php echo htmlspecialchars($row['state']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select class="filterSelect" id="seniorityFilter">
                        <option value="">All Seniority Levels</option>
                        <option value="L1">L1</option>
                        <option value="L2">L2</option>
                        <option value="L3">L3</option>
                        <option value="Expert">Expert</option>
                    </select>

                    <input
                        type="text"
                        placeholder="Search by name or expertise..."
                        class="searchInput"
                        id="searchInput"
                    />

                    <button class="clearFilters" onclick="clearAllFilters()">
                        <i class="fas fa-times"></i> Clear All Filters
                    </button>
                </div>

               <div style="flex: 1;">
                    <!--<div class="resultsCount" id="resultsCount"></div>-->
                    <div class="membersGrid" id="membersGrid">
                        <?php foreach ($members as $member): ?>
                            <div class="profileCard" 
                                 data-name="<?php echo htmlspecialchars(strtolower($member['first_name'] . ' ' . $member['last_name'])); ?>"
                                 data-expertise="<?php echo htmlspecialchars($member['area_of_expertise']); ?>" 
                                 data-expertise-lower="<?php echo htmlspecialchars(strtolower($member['area_of_expertise'])); ?>"
                                 data-state="<?php echo htmlspecialchars($member['state']); ?>" 
                                 data-seniority="<?php echo htmlspecialchars($member['seniority'] ?? ''); ?>">
                                <div class="profileImage">
                                    <?php $profileImageUrl = getProfileImageUrl($member['profile_image']); ?>
                                    <img src="<?php echo $profileImageUrl; ?>" 
                                         alt="<?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?>"
                                         onerror="this.src='https://sarathicooperative.org/img/avatar.png'" />
                                </div>
                                
                                <h3><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></h3>
                                <p class="expertise"><?php echo htmlspecialchars($member['area_of_expertise']); ?></p>
                                
                                <a href="mailto:<?php echo htmlspecialchars($member['email']); ?>" class="connectButton">
                                    <i class="fas fa-envelope"></i> Connect
                                </a>
                                
                                <div class="knowMore" onclick="showModal(<?php echo htmlspecialchars(json_encode($member)); ?>)">
                                    <i class="fas fa-info-circle"></i> Know More
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="noResults" id="noResults" style="display: none;">
                        <i class="fas fa-search" style="font-size: 2rem; margin-bottom: 1rem; color: #ccc;"></i>
                        <p>No Sarathians found matching your search criteria.</p>
                        <p>Try adjusting your filters or search terms.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Template -->
    <div class="modal" id="memberModal">
        <div class="modalContent">
            <button class="closeButton" onclick="hideModal()">×</button>
            <div id="modalContent"></div>
        </div>
    </div>

    <script>
        // Filter functionality
        const searchInput = document.getElementById('searchInput');
        const expertiseFilter = document.getElementById('expertiseFilter');
        const stateFilter = document.getElementById('stateFilter');
        const seniorityFilter = document.getElementById('seniorityFilter');
        const membersGrid = document.getElementById('membersGrid');
        const noResults = document.getElementById('noResults');
        const resultsCount = document.getElementById('resultsCount');

        function filterMembers() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const expertise = expertiseFilter.value;
            const state = stateFilter.value;
            const seniority = seniorityFilter.value;

            const cards = membersGrid.getElementsByClassName('profileCard');
            let visibleCount = 0;

            Array.from(cards).forEach(card => {
                const name = card.dataset.name;
                const cardExpertise = card.dataset.expertise;
                const cardExpertiseLower = card.dataset.expertiseLower;
                const cardState = card.dataset.state;
                const cardSeniority = card.dataset.seniority;

                // Enhanced search - matches name OR expertise
                const matchesSearch = !searchTerm || 
                    name.includes(searchTerm) || 
                    cardExpertiseLower.includes(searchTerm);
                
                const matchesExpertise = !expertise || cardExpertise === expertise;
                const matchesState = !state || cardState === state;
                const matchesSeniority = !seniority || cardSeniority === seniority;

                const isVisible = matchesSearch && matchesExpertise && matchesState && matchesSeniority;
                
                card.style.display = isVisible ? 'flex' : 'none';
                if (isVisible) visibleCount++;
            });

            // Update results count and show/hide no results message
            updateResultsDisplay(visibleCount);
        }

        function updateResultsDisplay(count) {
            const totalMembers = membersGrid.getElementsByClassName('profileCard').length;
            
            if (count === 0) {
                noResults.style.display = 'block';
                membersGrid.style.display = 'none';
                resultsCount.textContent = 'No results found';
            } else {
                noResults.style.display = 'none';
                membersGrid.style.display = 'grid';
                resultsCount.textContent = `Showing ${count} of ${totalMembers} Sarathians`;
            }
        }

        function clearAllFilters() {
            searchInput.value = '';
            expertiseFilter.value = '';
            stateFilter.value = '';
            seniorityFilter.value = '';
            filterMembers();
        }

        // Event listeners
        searchInput.addEventListener('input', filterMembers);
        expertiseFilter.addEventListener('change', filterMembers);
        stateFilter.addEventListener('change', filterMembers);
        seniorityFilter.addEventListener('change', filterMembers);

        // Initialize results count
        updateResultsDisplay(membersGrid.getElementsByClassName('profileCard').length);

        // Helper function to get profile image URL
        function getProfileImageUrl(profileImage) {
            const baseUrl = 'https://sarathicooperative.org';
            const uploadsPath = '/admin/uploads/members/';
            const defaultImage = '/img/avatar.png';
            
            if (profileImage && profileImage.trim() !== '') {
                // Check if the profile_image already contains the full path
                if (profileImage.includes('admin/uploads/members/')) {
                    // If it already contains the path, just add the base URL
                    return baseUrl + '/' + profileImage.replace(/^\/+/, '');
                } else {
                    // If it's just the filename, add the full path
                    return baseUrl + uploadsPath + profileImage;
                }
            } else {
                return baseUrl + defaultImage;
            }
        }

        // Modal functionality
        function showModal(member) {
            const modal = document.getElementById('memberModal');
            const modalContent = document.getElementById('modalContent');

            const profileImageUrl = getProfileImageUrl(member.profile_image);

            modalContent.innerHTML = `
                <div class="modalHeader">
                    <img src="${profileImageUrl}" 
                         alt="${member.first_name} ${member.last_name}"
                         onerror="this.src='https://sarathicooperative.org/img/avatar.png'" />
                    <div>
                        <h2>${member.first_name} ${member.last_name}</h2>
                        <p class="cursive">${member.area_of_expertise}</p>
                    </div>
                </div>

                <div class="modalBody">
                    <section>
                        <h3>Personal Information</h3>
                        <p><strong>Location:</strong> ${member.city || 'Not specified'}, ${member.state || 'Not specified'}</p>
                        <p><strong>Qualification:</strong> ${member.highest_qualification || 'Not specified'}</p>
                        ${member.seniority ? `<p><strong>Seniority Level:</strong> ${member.seniority}</p>` : ''}
                    </section>

                    <section>
                        <h3>Journey</h3>
                        <p>${member.journey || 'No journey information available.'}</p>
                    </section>

                    <a href="mailto:${member.email}" class="connectButton">
                        <i class="fas fa-envelope"></i> Connect with ${member.first_name}
                    </a>
                </div>
            `;

            modal.style.display = 'flex';
        }

        function hideModal() {
            document.getElementById('memberModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('memberModal');
            if (event.target === modal) {
                hideModal();
            }
        }

        // Add loading state for images
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('.profileImage img');
            images.forEach(img => {
                img.addEventListener('load', function() {
                    this.classList.remove('loading');
                });
                img.addEventListener('error', function() {
                    this.classList.remove('loading');
                });
                img.classList.add('loading');
            });
        });
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>