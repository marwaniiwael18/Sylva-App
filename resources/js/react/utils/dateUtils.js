/**
 * Date Utilities
 * Helper functions for date formatting and manipulation
 */

/**
 * Format date to readable string
 */
export const formatDate = (date, options = {}) => {
  if (!date) return ''
  
  const defaultOptions = {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  }
  
  return new Date(date).toLocaleDateString('en-US', { ...defaultOptions, ...options })
}

/**
 * Format date to short format
 */
export const formatDateShort = (date) => {
  return formatDate(date, { month: 'short', day: 'numeric' })
}

/**
 * Format time
 */
export const formatTime = (time) => {
  if (!time) return ''
  
  return new Date(`1970-01-01T${time}`).toLocaleTimeString('en-US', {
    hour: 'numeric',
    minute: '2-digit',
    hour12: true
  })
}

/**
 * Get relative time (e.g., "2 hours ago")
 */
export const getRelativeTime = (date) => {
  if (!date) return ''
  
  const now = new Date()
  const past = new Date(date)
  const diffInSeconds = Math.floor((now - past) / 1000)
  
  if (diffInSeconds < 60) return 'just now'
  if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} minutes ago`
  if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`
  if (diffInSeconds < 2592000) return `${Math.floor(diffInSeconds / 86400)} days ago`
  
  return formatDate(date)
}

/**
 * Check if date is today
 */
export const isToday = (date) => {
  if (!date) return false
  
  const today = new Date()
  const checkDate = new Date(date)
  
  return today.toDateString() === checkDate.toDateString()
}

/**
 * Check if date is in the future
 */
export const isFuture = (date) => {
  if (!date) return false
  
  return new Date(date) > new Date()
}

/**
 * Get days until date
 */
export const getDaysUntil = (date) => {
  if (!date) return 0
  
  const today = new Date()
  const targetDate = new Date(date)
  const diffInTime = targetDate.getTime() - today.getTime()
  const diffInDays = Math.ceil(diffInTime / (1000 * 3600 * 24))
  
  return diffInDays
}