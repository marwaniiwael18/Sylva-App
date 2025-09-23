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

  // Simulate checking for existing session on mount
  useEffect(() => {
    const checkAuthState = async () => {
      try {
        // Check localStorage for saved user data
        const savedUser = localStorage.getItem('sylva_user')
        if (savedUser) {
          setUser(JSON.parse(savedUser))
        }
      } catch (error) {
        console.error('Error checking auth state:', error)
      } finally {
        setLoading(false)
      }
    }

    checkAuthState()
  }, [])

  const login = async (email, password) => {
    try {
      setLoading(true)
      
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 1000))
      
      // Mock user data based on email
      const mockUser = {
        id: Math.random().toString(36).substr(2, 9),
        name: email.split('@')[0].charAt(0).toUpperCase() + email.split('@')[0].slice(1),
        email,
        role: email.includes('admin') ? 'municipality' : 
              email.includes('org') ? 'association' : 'citizen',
        avatar: `https://ui-avatars.com/api/?name=${email.split('@')[0]}&background=22c55e&color=fff`,
        joinedDate: new Date().toISOString(),
        stats: {
          treesPlanted: Math.floor(Math.random() * 50) + 10,
          projectsJoined: Math.floor(Math.random() * 20) + 3,
          eventsAttended: Math.floor(Math.random() * 15) + 2,
          co2Saved: Math.floor(Math.random() * 500) + 100,
          spacesGreened: Math.floor(Math.random() * 10) + 1,
        },
        badges: ['Green Hero', 'Tree Planter', 'Community Builder']
      }
      
      setUser(mockUser)
      localStorage.setItem('sylva_user', JSON.stringify(mockUser))
      
      return { success: true, user: mockUser }
    } catch (error) {
      console.error('Login error:', error)
      return { success: false, error: 'Login failed. Please try again.' }
    } finally {
      setLoading(false)
    }
  }

  const signup = async (userData) => {
    try {
      setLoading(true)
      
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 1500))
      
      const mockUser = {
        id: Math.random().toString(36).substr(2, 9),
        name: userData.name,
        email: userData.email,
        role: userData.role,
        avatar: `https://ui-avatars.com/api/?name=${userData.name}&background=22c55e&color=fff`,
        joinedDate: new Date().toISOString(),
        stats: {
          treesPlanted: 0,
          projectsJoined: 0,
          eventsAttended: 0,
          co2Saved: 0,
          spacesGreened: 0,
        },
        badges: ['New Member']
      }
      
      setUser(mockUser)
      localStorage.setItem('sylva_user', JSON.stringify(mockUser))
      
      return { success: true, user: mockUser }
    } catch (error) {
      console.error('Signup error:', error)
      return { success: false, error: 'Signup failed. Please try again.' }
    } finally {
      setLoading(false)
    }
  }

  const logout = () => {
    setUser(null)
    localStorage.removeItem('sylva_user')
  }

  const forgotPassword = async (email) => {
    try {
      setLoading(true)
      
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 1000))
      
      return { success: true, message: 'Password reset link sent to your email!' }
    } catch (error) {
      console.error('Forgot password error:', error)
      return { success: false, error: 'Failed to send reset email. Please try again.' }
    } finally {
      setLoading(false)
    }
  }

  const value = {
    user,
    login,
    signup,
    logout,
    forgotPassword,
    loading
  }

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  )
}