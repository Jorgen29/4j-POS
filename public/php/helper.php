<?php

/**
 * Get role name from role ID
 * @param int $roleId Role ID (1 = user, 0 = admin)
 * @return string Role name
 */
function getRoleName($roleId) {
    $roles = [
        0 => 'Admin',
        1 => 'User'
    ];
    
    return isset($roles[$roleId]) ? $roles[$roleId] : 'Unknown';
}

/**
 * Get status name from status ID
 * @param int $statusId Status ID (0 = inactive, 1 = active)
 * @return string Status name
 */
function getStatusName($statusId) {
    $statuses = [
        0 => 'Inactive',
        1 => 'Active'
    ];
    
    return isset($statuses[$statusId]) ? $statuses[$statusId] : 'Unknown';
}

/**
 * Get status badge HTML
 * @param int $statusId Status ID (0 = inactive, 1 = active)
 * @return string HTML badge
 */
function getStatusBadge($statusId) {
    if ($statusId == 1) {
        return '<span class="px-2 py-1 rounded text-xs bg-green/10 text-green">Active</span>';
    } else {
        return '<span class="px-2 py-1 rounded text-xs bg-redsoft/10 text-redsoft">Inactive</span>';
    }
}

/**
 * Get role badge HTML
 * @param int $roleId Role ID (1 = user, 0 = admin)
 * @return string HTML badge
 */
function getRoleBadge($roleId) {
    if ($roleId == 0) {
        return '<span class="px-2 py-1 rounded text-xs bg-gold/10 text-gold">Admin</span>';
    } else {
        return '<span class="px-2 py-1 rounded text-xs bg-blue/10 text-blue">User</span>';
    }
}

?>
