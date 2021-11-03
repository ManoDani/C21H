<?php

use Phinx\Migration\AbstractMigration;

class CoursesMigration extends AbstractMigration
{
    public function change()
    {
        $courses = $this->table('courses');
        $courses->addColumn('title', 'string');
        $courses->addColumn('description', 'string');
        $courses->addColumn('long_description', 'text', ['null' => true]);
        $courses->addColumn('image', 'string', ['null' => true]);
        $courses->addColumn('status', 'boolean', [
            'default' => false,
        ]);
        $courses->addColumn('price', 'decimal', [
            'precision' => 9,
            'scale' => 2,
            'null' => true,
        ]);
        $courses->addTimestamps();
        $courses->create();

        $modules = $this->table('modules');
        $modules->addColumn('number', 'integer');
        $modules->addColumn('title', 'string');
        $modules->addColumn('description', 'string');
        $modules->addColumn('course_id', 'integer');
        $modules->addTimestamps();
        $modules->addForeignKey('course_id', 'courses', 'id', [
            'delete' => 'CASCADE',
            'update' => 'NO_ACTION',
        ]);
        $modules->create();

        $levels = $this->table('levels');
        $levels->addColumn('number', 'integer');
        $levels->addColumn('title', 'string');
        $levels->addColumn('content', 'text');
        $levels->addColumn('video', 'string', ['null' => true]);
        $levels->addColumn('module_id', 'integer');
        $levels->addTimestamps();
        $levels->addForeignKey('module_id', 'modules', 'id', [
            'delete' => 'CASCADE',
            'update' => 'NO_ACTION',
        ]);
        $levels->create();

        $usersCourses = $this->table('users_courses', [
            'id' => false,
            'primary_key' => ['user_id', 'course_id']
        ]);
        $usersCourses->addColumn('user_id', 'integer', ["null" => true]);
        $usersCourses->addColumn('course_id', 'integer', ["null" => true]);
        $usersCourses->addForeignKey('user_id', 'users', 'id', [
            'delete' => 'CASCADE',
            'update' => 'NO_ACTION',
        ]);
        $usersCourses->addForeignKey('course_id', 'courses', 'id', [
            'delete' => 'CASCADE',
            'update' => 'NO_ACTION',
        ]);
        $usersCourses->create();

        $attachments = $this->table('attachments');
        $attachments->addColumn('file_name', 'string');
        $attachments->addColumn('path', 'string');
        $attachments->addColumn('level_id', 'integer');
        $attachments->addTimestamps();
        $attachments->addForeignKey('level_id', 'levels', 'id', [
            'delete'=> 'CASCADE',
            'update'=> 'NO_ACTION'
        ]);
        $attachments->create();
    }
}
