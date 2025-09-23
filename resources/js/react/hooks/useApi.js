/**
 * useApi Hook
 * Custom hook for API calls with loading, error, and data states
 */

import { useState, useEffect } from 'react'

export const useApi = (apiFunction, dependencies = []) => {
  const [data, setData] = useState(null)
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(null)

  const fetchData = async () => {
    try {
      setLoading(true)
      setError(null)
      const result = await apiFunction()
      setData(result)
    } catch (err) {
      setError(err.message || 'An error occurred')
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    fetchData()
  }, dependencies)

  const refetch = () => {
    fetchData()
  }

  return { data, loading, error, refetch }
}

/**
 * useApiCall Hook
 * Custom hook for manual API calls (not automatic on mount)
 */
export const useApiCall = () => {
  const [data, setData] = useState(null)
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState(null)

  const execute = async (apiFunction) => {
    try {
      setLoading(true)
      setError(null)
      const result = await apiFunction()
      setData(result)
      return result
    } catch (err) {
      const errorMessage = err.message || 'An error occurred'
      setError(errorMessage)
      throw err
    } finally {
      setLoading(false)
    }
  }

  const reset = () => {
    setData(null)
    setError(null)
    setLoading(false)
  }

  return { data, loading, error, execute, reset }
}