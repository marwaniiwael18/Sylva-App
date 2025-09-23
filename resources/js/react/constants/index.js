/**
 * Application Constants
 * Central place for all app constants
 */

// API Endpoints
export const API_ENDPOINTS = {
  AUTH: {
    LOGIN: '/auth/login',
    REGISTER: '/auth/register',
    LOGOUT: '/auth/logout',
    FORGOT_PASSWORD: '/auth/forgot-password',
    USER: '/user'
  },
  PROJECTS: {
    BASE: '/projects',
    JOIN: (id) => `/projects/${id}/join`,
    LEAVE: (id) => `/projects/${id}/leave`
  },
  EVENTS: {
    BASE: '/events',
    RSVP: (id) => `/events/${id}/rsvp`
  },
  DASHBOARD: {
    STATS: '/dashboard/stats',
    ACTIVITY: '/dashboard/activity',
    IMPACT: '/dashboard/impact',
    UPCOMING_EVENTS: '/dashboard/upcoming-events',
    PROJECTS: '/dashboard/projects'
  }
}

// Local Storage Keys
export const STORAGE_KEYS = {
  AUTH_TOKEN: 'auth_token',
  USER_DATA: 'user_data',
  THEME: 'theme_preference',
  LANGUAGE: 'language_preference'
}

// Application Routes
export const ROUTES = {
  HOME: '/',
  DASHBOARD: '/dashboard',
  PROJECTS: '/projects',
  PROJECT_DETAIL: '/projects/:id',
  EVENTS: '/events',
  EVENT_DETAIL: '/events/:id',
  MAP: '/map',
  IMPACT: '/impact',
  FEEDBACK: '/feedback',
  LOGIN: '/login',
  REGISTER: '/register',
  FORGOT_PASSWORD: '/forgot-password'
}

// Project Status
export const PROJECT_STATUS = {
  PLANNING: 'planning',
  ACTIVE: 'active',
  COMPLETED: 'completed',
  CANCELLED: 'cancelled'
}

// Event Status
export const EVENT_STATUS = {
  UPCOMING: 'upcoming',
  ONGOING: 'ongoing',
  COMPLETED: 'completed',
  CANCELLED: 'cancelled'
}

// User Roles
export const USER_ROLES = {
  USER: 'user',
  MODERATOR: 'moderator',
  ADMIN: 'admin'
}

// Theme Options
export const THEMES = {
  LIGHT: 'light',
  DARK: 'dark',
  SYSTEM: 'system'
}

// Pagination
export const PAGINATION = {
  DEFAULT_PAGE_SIZE: 10,
  MAX_PAGE_SIZE: 100
}

// File Upload
export const UPLOAD_LIMITS = {
  MAX_FILE_SIZE: 5 * 1024 * 1024, // 5MB
  ALLOWED_IMAGE_TYPES: ['image/jpeg', 'image/png', 'image/gif'],
  ALLOWED_DOCUMENT_TYPES: ['application/pdf', 'text/plain', 'application/msword']
}

// Map Configuration
export const MAP_CONFIG = {
  DEFAULT_CENTER: [-74.006, 40.7128], // New York City
  DEFAULT_ZOOM: 12,
  MAX_ZOOM: 20,
  MIN_ZOOM: 3
}