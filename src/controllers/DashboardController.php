<?php

namespace Algom\Academia1\controllers;

use Algom\Academia1\repositories\UserRepository;
use Algom\Academia1\repositories\SubjectRepository;
use Algom\Academia1\repositories\GradeRepository;
use Algom\Academia1\repositories\StudentRepository;

class DashboardController {
    private $userRepository;
    private $subjectRepository;
    private $gradeRepository;
    private $studentRepository;

    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->subjectRepository = new SubjectRepository();
        $this->gradeRepository = new GradeRepository();
        $this->studentRepository = new StudentRepository();
    }

    public function getDashboardStats($userId, $role) {
        $stats = [
            'totalStudents' => 0,
            'totalSubjects' => 0,
            'totalGrades' => 0,
            'recentGrades' => []
        ];

        switch ($role) {
            case 'gestor':
                $stats['totalStudents'] = $this->studentRepository->getTotalStudents();
                $stats['totalSubjects'] = $this->subjectRepository->getTotalSubjects();
                $stats['totalGrades'] = $this->gradeRepository->getTotalGrades();
                $stats['recentGrades'] = $this->gradeRepository->getRecentGrades(5);
                break;

            case 'profesor':
                $teacherSubjects = $this->subjectRepository->getSubjectsByTeacher($userId);
                $stats['totalSubjects'] = count($teacherSubjects);
                $stats['totalStudents'] = $this->studentRepository->getTotalStudentsByTeacher($userId);
                $stats['totalGrades'] = $this->gradeRepository->getTotalGradesByTeacher($userId);
                $stats['recentGrades'] = $this->gradeRepository->getRecentGradesByTeacher($userId, 5);
                break;

            case 'tutor':
                $studentIds = $this->studentRepository->getStudentsByTutor($userId);
                $stats['totalStudents'] = count($studentIds);
                $stats['recentGrades'] = $this->gradeRepository->getRecentGradesByStudents($studentIds, 5);
                break;
        }

        return $stats;
    }
}
