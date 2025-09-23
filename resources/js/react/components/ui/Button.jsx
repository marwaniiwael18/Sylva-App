/**
 * Button Component
 * Reusable button with different variants and sizes
 */

import React from 'react'
import LoadingSpinner from './LoadingSpinner'

const Button = ({
  children,
  variant = 'primary',
  size = 'medium',
  disabled = false,
  loading = false,
  fullWidth = false,
  className = '',
  ...props
}) => {
  const baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-4 disabled:opacity-50 disabled:cursor-not-allowed'

  const variantClasses = {
    primary: 'bg-gradient-to-r from-green-600 to-green-600 text-white hover:from-green-700 hover:to-green-700 hover:shadow-lg hover:scale-105 focus:ring-green-200',
    secondary: 'bg-white text-green-600 border border-green-200 hover:bg-green-50 hover:shadow-md focus:ring-green-200',
    outline: 'bg-transparent text-green-600 border border-green-300 hover:bg-green-50 focus:ring-green-200',
    danger: 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-200',
    ghost: 'bg-transparent text-gray-600 hover:bg-gray-100 focus:ring-gray-200'
  }

  const sizeClasses = {
    small: 'px-3 py-2 text-sm',
    medium: 'px-6 py-3 text-base',
    large: 'px-8 py-4 text-lg'
  }

  const widthClasses = fullWidth ? 'w-full' : ''

  const classes = `${baseClasses} ${variantClasses[variant]} ${sizeClasses[size]} ${widthClasses} ${className}`

  return (
    <button
      className={classes}
      disabled={disabled || loading}
      {...props}
    >
      {loading && (
        <LoadingSpinner 
          size="small" 
          color={variant === 'primary' ? 'white' : 'primary'} 
          className="mr-2"
        />
      )}
      {children}
    </button>
  )
}

export default Button