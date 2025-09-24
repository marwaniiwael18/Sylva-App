/**
 * useApi Hook
 * Custom hook for API calls with loading, error, and data states
 */

import { useState, useCallback } from 'react'

/**
 * Hook for manual API calls with loading and error handling
 */
export const useApi = (apiFunction) => {
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState(null)

  const execute = useCallback(async (...args) => {
    try {
      setLoading(true)
      setError(null)
      const result = await apiFunction(...args)
      return result
    } catch (err) {
      const errorMessage = err.response?.data?.message || err.message || 'An error occurred'
      setError(errorMessage)
      return {
        success: false,
        message: errorMessage,
        error: errorMessage
      }
    } finally {
      setLoading(false)
    }
  }, [apiFunction])

  const reset = useCallback(() => {
    setError(null)
    setLoading(false)
  }, [])

  return { 
    loading, 
    error, 
    execute, 
    reset 
  }
}

/**
 * useApiCall Hook - Legacy compatibility
 */
export const useApiCall = useApi

export default useApi