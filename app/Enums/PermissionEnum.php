<?php

namespace App\Enums;

enum PermissionEnum: string
{
    case KPI_CREATE = 'KPI.CREATE';
    case KPI_READ = 'KPI.READ';
    case KPI_READTRASHED = 'KPI.READTRASHED';
    case KPI_UPDATE = 'KPI.UPDATE';
    case KPI_DELETE = 'KPI.DELETE';
    case KPI_RESTORE = 'KPI.RESTORE';

    case USER_CREATE = 'USER.CREATE';
    case USER_READ = 'USER.READ';
    case USER_READTRASHED = 'USER.READTRASHED';
    case USER_UPDATE = 'USER.UPDATE';
    case USER_DELETE = 'USER.DELETE';
    case USER_RESTORE = 'USER.RESTORE';

    case ASSIGNMENT_CREATE = 'ASSIGNMENT.CREATE';
    case ASSIGNMENT_READ = 'ASSIGNMENT.READ';
    case ASSIGNMENT_READTRASHED = 'ASSIGNMENT.READTRASHED';
    case ASSIGNMENT_UPDATE = 'ASSIGNMENT.UPDATE';
    case ASSIGNMENT_DELETE = 'ASSIGNMENT.DELETE';
    case ASSIGNMENT_RESTORE = 'ASSIGNMENT.RESTORE';

    case ASSIGNMENTFILE_CREATE = 'ASSIGNMENTFILE.CREATE';
    case ASSIGNMENTFILE_READ = 'ASSIGNMENTFILE.READ';
    case ASSIGNMENTFILE_READTRASHED = 'ASSIGNMENTFILE.READTRASHED';
    case ASSIGNMENTFILE_UPDATE = 'ASSIGNMENTFILE.UPDATE';
    case ASSIGNMENTFILE_DELETE = 'ASSIGNMENTFILE.DELETE';
    case ASSIGNMENTFILE_RESTORE = 'ASSIGNMENTFILE.RESTORE';

    case ASSIGNMENTIMAGE_CREATE = 'ASSIGNMENTIMAGE.CREATE';
    case ASSIGNMENTIMAGE_READ = 'ASSIGNMENTIMAGE.READ';
    case ASSIGNMENTIMAGE_READTRASHED = 'ASSIGNMENTIMAGE.READTRASHED';
    case ASSIGNMENTIMAGE_UPDATE = 'ASSIGNMENTIMAGE.UPDATE';
    case ASSIGNMENTIMAGE_DELETE = 'ASSIGNMENTIMAGE.DELETE';
    case ASSIGNMENTIMAGE_RESTORE = 'ASSIGNMENTIMAGE.RESTORE';

    case ATTENDANCE_CREATE = 'ATTENDANCE.CREATE';
    case ATTENDANCE_READ = 'ATTENDANCE.READ';
    case ATTENDANCE_READTRASHED = 'ATTENDANCE.READTRASHED';
    case ATTENDANCE_UPDATE = 'ATTENDANCE.UPDATE';
    case ATTENDANCE_DELETE = 'ATTENDANCE.DELETE';
    case ATTENDANCE_RESTORE = 'ATTENDANCE.RESTORE';

    case ATTENDANCEFILE_CREATE = 'ATTENDANCEFILE.CREATE';
    case ATTENDANCEFILE_READ = 'ATTENDANCEFILE.READ';
    case ATTENDANCEFILE_READTRASHED = 'ATTENDANCEFILE.READTRASHED';
    case ATTENDANCEFILE_UPDATE = 'ATTENDANCEFILE.UPDATE';
    case ATTENDANCEFILE_DELETE = 'ATTENDANCEFILE.DELETE';
    case ATTENDANCEFILE_RESTORE = 'ATTENDANCEFILE.RESTORE';

    case WORK_CREATE = 'WORK.CREATE';
    case WORK_READ = 'WORK.READ';
    case WORK_READTRASHED = 'WORK.READTRASHED';
    case WORK_UPDATE = 'WORK.UPDATE';
    case WORK_DELETE = 'WORK.DELETE';
    case WORK_RESTORE = 'WORK.RESTORE';

    case WORKRATIO_CREATE = 'WORKRATIO.CREATE';
    case WORKRATIO_READ = 'WORKRATIO.READ';
    case WORKRATIO_READTRASHED = 'WORKRATIO.READTRASHED';
    case WORKRATIO_UPDATE = 'WORKRATIO.UPDATE';
    case WORKRATIO_DELETE = 'WORKRATIO.DELETE';
    case WORKRATIO_RESTORE = 'WORKRATIO.RESTORE';

    case PAYCHECKFILE_CREATE = 'PAYCHECKFILE.CREATE';
    case PAYCHECKFILE_READ = 'PAYCHECKFILE.READ';
    case PAYCHECKFILE_READTRASHED = 'PAYCHECKFILE.READTRASHED';
    case PAYCHECKFILE_UPDATE = 'PAYCHECKFILE.UPDATE';
    case PAYCHECKFILE_DELETE = 'PAYCHECKFILE.DELETE';
    case PAYCHECKFILE_RESTORE = 'PAYCHECKFILE.RESTORE';

    case PERMIT_CREATE = 'PERMIT.CREATE';
    case PERMIT_READ = 'PERMIT.READ';
    case PERMIT_READTRASHED = 'PERMIT.READTRASHED';
    case PERMIT_UPDATE = 'PERMIT.UPDATE';
    case PERMIT_DELETE = 'PERMIT.DELETE';
    case PERMIT_RESTORE = 'PERMIT.RESTORE';
}
