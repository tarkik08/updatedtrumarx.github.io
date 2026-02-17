-- Add status column to existing tables if they don't have it
-- Run this SQL in your cPanel phpMyAdmin or MySQL interface

-- Add status to consultations table
ALTER TABLE `consultations` 
ADD COLUMN IF NOT EXISTS `status` VARCHAR(20) DEFAULT 'pending' AFTER `submitted_at`;

-- Add status to internships table
ALTER TABLE `internships` 
ADD COLUMN IF NOT EXISTS `status` VARCHAR(20) DEFAULT 'pending' AFTER `submitted_at`;

-- Add status to job_applications table
ALTER TABLE `job_applications` 
ADD COLUMN IF NOT EXISTS `status` VARCHAR(20) DEFAULT 'pending' AFTER `submitted_at`;
