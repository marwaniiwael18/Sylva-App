import React, { createContext, useContext, useState, useEffect } from 'react'

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
    const token = localStorage.getItem('auth_token')
    const userData = localStorage.getItem('user_data')
    
    if (token && userData) {
      setUser(JSON.parse(userData))
      // Set axios default header for authenticated requests
      window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
    }
    setLoading(false)
  }, [])

  const login = async (email, password) => {
    try {
      const response = await window.axios.post('/auth/login', {
        email,
        password
      })

      if (response.data.success) {
        const { user, token } = response.data
        setUser(user)
        localStorage.setItem('auth_token', token)
        localStorage.setItem('user_data', JSON.stringify(user))
        
        // Set axios default header for future requests
        window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        
        return { success: true }
      } else {
        return { success: false, message: response.data.message }
      }
    } catch (error) {
      return { 
        success: false, 
        message: error.response?.data?.message || 'Login failed. Please try again.' 
      }
    }
  }

  const signup = async (name, email, password, passwordConfirmation) => {
    try {
      const response = await window.axios.post('/auth/register', {
        name,
        email,
        password,
        password_confirmation: passwordConfirmation
      })

      if (response.data.success) {
        const { user, token } = response.data
        setUser(user)
        localStorage.setItem('auth_token', token)
        localStorage.setItem('user_data', JSON.stringify(user))
        
        // Set axios default header for future requests
        window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        
        return { success: true }
      } else {
        return { success: false, message: response.data.message }
      }
    } catch (error) {
      return { 
        success: false, 
        message: error.response?.data?.message || 'Registration failed. Please try again.' 
      }
    }
  }

  const logout = async () => {
    try {
      await window.axios.post('/auth/logout')
    } catch (error) {
      console.error('Logout error:', error)
    }
    
    // Clear local storage and state regardless of API response
    setUser(null)
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user_data')
    localStorage.removeItem('sylva_user') // Remove old key for compatibility
    delete window.axios.defaults.headers.common['Authorization']
  }

  const forgotPassword = async (email) => {
    try {
      const response = await window.axios.post('/auth/forgot-password', { email })
      return { 
        success: response.data.success, 
        message: response.data.message 
      }
    } catch (error) {
      return { 
        success: false, 
        message: error.response?.data?.message || 'Failed to send reset email.' 
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