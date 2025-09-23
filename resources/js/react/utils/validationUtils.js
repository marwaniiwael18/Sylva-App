/**
 * Validation Utilities
 * Helper functions for form validation
 */

/**
 * Validate email format
 */
export const isValidEmail = (email) => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

/**
 * Validate password strength
 */
export const validatePassword = (password) => {
  const minLength = 8
  const hasUpperCase = /[A-Z]/.test(password)
  const hasLowerCase = /[a-z]/.test(password)
  const hasNumbers = /\d/.test(password)
  const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password)
  
  const score = [
    password.length >= minLength,
    hasUpperCase,
    hasLowerCase,
    hasNumbers,
    hasSpecialChar
  ].filter(Boolean).length
  
  return {
    isValid: score >= 3,
    score,
    requirements: {
      minLength: password.length >= minLength,
      hasUpperCase,
      hasLowerCase,
      hasNumbers,
      hasSpecialChar
    }
  }
}

/**
 * Validate phone number
 */
export const isValidPhone = (phone) => {
  const phoneRegex = /^\+?[\d\s\-\(\)]{10,}$/
  return phoneRegex.test(phone)
}

/**
 * Validate URL
 */
export const isValidUrl = (url) => {
  try {
    new URL(url)
    return true
  } catch {
    return false
  }
}

/**
 * Check if field is required and not empty
 */
export const isRequired = (value) => {
  return value !== null && value !== undefined && value.toString().trim() !== ''
}

/**
 * Validate minimum length
 */
export const hasMinLength = (value, minLength) => {
  return value && value.length >= minLength
}

/**
 * Validate maximum length
 */
export const hasMaxLength = (value, maxLength) => {
  return !value || value.length <= maxLength
}

/**
 * Validate numeric value
 */
export const isNumeric = (value) => {
  return !isNaN(parseFloat(value)) && isFinite(value)
}

/**
 * Validate positive number
 */
export const isPositive = (value) => {
  return isNumeric(value) && parseFloat(value) > 0
}

/**
 * Validate date format (YYYY-MM-DD)
 */
export const isValidDate = (date) => {
  const dateRegex = /^\d{4}-\d{2}-\d{2}$/
  if (!dateRegex.test(date)) return false
  
  const dateObj = new Date(date)
  return dateObj instanceof Date && !isNaN(dateObj)
}