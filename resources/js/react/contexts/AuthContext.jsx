import React, { createContext, useContext, useState, useEffect } from 'react'
import { authService } from '../services/authService'
import { STORAGE_KEYS } from '../constants'

const AuthContext = createContext()

export const useAuth = () => {
  const context = useContext(AuthContext)
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider')
  }
  return context
}

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    // Check if user is logged in on app start
    const token = localStorage.getItem(STORAGE_KEYS.AUTH_TOKEN)
    const userData = localStorage.getItem(STORAGE_KEYS.USER_DATA)
    
    if (token && userData) {
      setUser(JSON.parse(userData))
      // Set axios default header for authenticated requests
      window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
    }
    setLoading(false)
  }, [])

  const login = async (email, password) => {
    try {
      const response = await authService.login(email, password)

      if (response.success) {
        const { user, token } = response
        setUser(user)
        localStorage.setItem(STORAGE_KEYS.AUTH_TOKEN, token)
        localStorage.setItem(STORAGE_KEYS.USER_DATA, JSON.stringify(user))
        
        // Set axios default header for future requests
        window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        
        return { success: true }
      } else {
        return { success: false, message: response.message }
      }
    } catch (error) {
      return { 
        success: false, 
        message: error.message || 'Login failed. Please try again.' 
      }
    }
  }

  const signup = async (name, email, password, passwordConfirmation) => {
    try {
      const response = await authService.register({
        name,
        email,
        password,
        password_confirmation: passwordConfirmation
      })

      if (response.success) {
        const { user, token } = response
        setUser(user)
        localStorage.setItem(STORAGE_KEYS.AUTH_TOKEN, token)
        localStorage.setItem(STORAGE_KEYS.USER_DATA, JSON.stringify(user))
        
        // Set axios default header for future requests
        window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        
        return { success: true }
      } else {
        return { success: false, message: response.message }
      }
    } catch (error) {
      return { 
        success: false, 
        message: error.message || 'Registration failed. Please try again.' 
      }
    }
  }

  const logout = async () => {
    try {
      await authService.logout()
    } catch (error) {
      console.error('Logout error:', error)
    }
    
    // Clear local storage and state regardless of API response
    setUser(null)
    localStorage.removeItem(STORAGE_KEYS.AUTH_TOKEN)
    localStorage.removeItem(STORAGE_KEYS.USER_DATA)
    localStorage.removeItem('sylva_user') // Remove old key for compatibility
    delete window.axios.defaults.headers.common['Authorization']
  }

  const forgotPassword = async (email) => {
    try {
      const response = await authService.forgotPassword(email)
      return { 
        success: response.success, 
        message: response.message 
      }
    } catch (error) {
      return { 
        success: false, 
        message: error.message || 'Failed to send reset email.' 
      }
    }
  }

  const value = {
    user,
    loading,
    login,
    signup,
    logout,
    forgotPassword
  }

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  )
}