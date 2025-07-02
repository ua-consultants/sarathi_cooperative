<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sarathi CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="/admin/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="admin-wrapper">
        <nav class="navbar navbar-expand-lg fixed-top">
            <div class="container-fluid">
                <span class="navbar-brand">Sarathi CMS</span>
                <div class="ms-auto">
                    <div class="dropdown">
                        <button class="btn btn-link dropdown-toggle text-white" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> 
                            <span class="ms-1"><?php echo $_SESSION['email']; ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>