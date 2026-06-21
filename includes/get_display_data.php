<?php
function getCarBrands($conn) 
{
    $brands = [];
    $result = $conn->query("SELECT name, image, search_term FROM car_brands_display WHERE is_active = 1 ORDER BY display_order, name");
    
    if ($result && $result->num_rows > 0) 
    {
        while ($row = $result->fetch_assoc()) 
        {
            $brands[] = [
                'name' => $row['name'],
                'image' => $row['image'],
                'search_term' => $row['search_term']
            ];
        }
    }
    
    return $brands;
}

function getPopularParts($conn) 
{
    $parts = [];
    $result = $conn->query("SELECT name, short_name, image, category, category_short, search_term FROM popular_parts_display WHERE is_active = 1 ORDER BY display_order, name");
    
    if ($result && $result->num_rows > 0) 
    {
        while ($row = $result->fetch_assoc()) 
        {
            $parts[] = [
                'name' => $row['name'],
                'short_name' => $row['short_name'],
                'image' => $row['image'],
                'category' => $row['category'],
                'category_short' => $row['category_short'],
                'search_term' => $row['search_term']
            ];
        }
    }
    
    return $parts;
}

if (isset($_GET['action'])) 
{
    require_once("../config/link.php");
    
    header('Content-Type: application/json');
    
    if ($_GET['action'] === 'get_brands') 
    {
        echo json_encode(['success' => true, 'data' => getCarBrands($conn)]);
    } 
    else if ($_GET['action'] === 'get_parts') 
    {
        echo json_encode(['success' => true, 'data' => getPopularParts($conn)]);
    }

    exit;
}
?>