/**
 * Card Component
 * Reusable card container with different variants
 */

import React from 'react'

const Card = ({
  children,
  variant = 'default',
  interactive = false,
  className = '',
  ...props
}) => {
  const baseClasses = 'bg-white rounded-xl shadow-sm border border-gray-100'
  
  const variantClasses = {
    default: '',
    elevated: 'shadow-lg',
    outlined: 'border-2 border-gray-200',
    gradient: 'bg-gradient-to-br from-green-50 to-blue-50'
  }
  
  const interactiveClasses = interactive 
    ? 'transition-all duration-300 hover:shadow-xl hover:-translate-y-2 hover:border-green-200 cursor-pointer transform'
    : 'transition-all duration-300 hover:shadow-lg hover:-translate-y-1'

  const classes = `${baseClasses} ${variantClasses[variant]} ${interactiveClasses} ${className}`

  return (
    <div className={classes} {...props}>
      {children}
    </div>
  )
}

// Card sub-components
Card.Header = ({ children, className = '', ...props }) => (
  <div className={`px-6 py-4 border-b border-gray-100 ${className}`} {...props}>
    {children}
  </div>
)

Card.Body = ({ children, className = '', ...props }) => (
  <div className={`px-6 py-4 ${className}`} {...props}>
    {children}
  </div>
)

Card.Footer = ({ children, className = '', ...props }) => (
  <div className={`px-6 py-4 border-t border-gray-100 ${className}`} {...props}>
    {children}
  </div>
)

export default Card