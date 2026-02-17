<?php
function getCategoryProducts($conn, $category_type, $search_query = '', $brand_filter = '', $sort_type = 'default', $extra_filters = []) 
{
    $sql = "SELECT * FROM category_products WHERE category_type = ?";
    $params = [$category_type];
    $types = "s";
    
    if (!empty($search_query)) 
    {
        $sql .= " AND (title LIKE ? OR art LIKE ? OR brand LIKE ?)";
        $search_term = "%$search_query%";
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
        $types .= "sss";
    }
    
    if (!empty($brand_filter)) 
    {
        $sql .= " AND brand = ?";
        $params[] = $brand_filter;
        $types .= "s";
    }
    
    foreach ($extra_filters as $key => $value) 
    {
        if (!empty($value) && $value !== 'Все') 
        {
            $sql .= " AND $key = ?";
            $params[] = $value;
            $types .= "s";
        }
    }
    
    $sql .= " ORDER BY id";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];

    while ($row = $result->fetch_assoc()) 
    {
        $products[] = $row;
    }

    $stmt->close();

    switch ($sort_type) 
    {
        case 'price_asc':
            usort($products, function($a, $b) 
            {
                return $a['price'] - $b['price'];
            });
            break;
        case 'price_desc':
            usort($products, function($a, $b) 
            {
                return $b['price'] - $a['price'];
            });
            break;
        case 'name':
            usort($products, function($a, $b) 
            {
                return strcmp($a['title'], $b['title']);
            });
            break;
        case 'popular':
            usort($products, function($a, $b) 
            {
                if ($a['hit'] == $b['hit']) 
                {
                    return 0;
                }

                return $a['hit'] ? -1 : 1;
            });
            break;
        default:
            break;
    }
    
    return $products;
}

function getFilterOptions($conn, $category_type, $filter_field) 
{
    $sql = "SELECT DISTINCT $filter_field FROM category_products WHERE category_type = ? AND $filter_field IS NOT NULL AND $filter_field != '' ORDER BY $filter_field";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category_type);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $options = [];
    
    while ($row = $result->fetch_assoc()) 
    {
        $options[] = $row[$filter_field];
    }

    $stmt->close();
    return $options;
}


function buildCategoryQueryString($newParams = [], $excludeParams = []) 
{
    $params = array_merge($_GET, $newParams);
    
    foreach ($excludeParams as $param) 
    {
        unset($params[$param]);
    }
    
    foreach ($params as $key => $value) 
    {
        if ($value === '') 
        {
            unset($params[$key]);
        }
    }
    
    return http_build_query($params);
}
?>