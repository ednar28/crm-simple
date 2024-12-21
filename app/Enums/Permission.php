<?php

namespace App\Enums;

enum Permission: string
{
    case ADMIN_APP = 'Access the Admin App';

    case COMPANY_LIST = 'See the List of Companies';
    case COMPANY_CREATE = 'Create a Company';
    case COMPANY_SHOW = 'See a Company';
    case COMPANY_EDIT = 'Edit a Company';
    case COMPANY_DELETE = 'Delete a Company';

    case EMPLOYEE_LIST = 'See the List of Employees';
    case EMPLOYEE_CREATE = 'Create an Employee';
    case EMPLOYEE_SHOW = 'See an Employee';
    case EMPLOYEE_EDIT = 'Edit an Employee';
    case EMPLOYEE_DELETE = 'Delete an Employee';

    /**
     * Get all permissions.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
