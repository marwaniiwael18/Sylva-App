/**
 * Input Component
 * Reusable input field with validation states
 */

import React, { forwardRef } from 'react'

const Input = forwardRef(({
  label,
  error,
  helper,
  required = false,
  className = '',
  ...props
}, ref) => {
  const inputClasses = `
    w-full px-4 py-3 border rounded-lg transition-all duration-200 focus:outline-none focus:ring-4 focus:shadow-lg
    ${error 
      ? 'border-red-300 focus:border-red-500 focus:ring-red-100' 
      : 'border-gray-200 focus:border-green-500 focus:ring-green-100'
    }
    ${props.disabled ? 'bg-gray-50 text-gray-400 cursor-not-allowed' : 'bg-white'}
    ${className}
  `

  return (
    <div className="w-full">
      {label && (
        <label className="block text-sm font-medium text-gray-700 mb-2">
          {label}
          {required && <span className="text-red-500 ml-1">*</span>}
        </label>
      )}
      
      <input
        ref={ref}
        className={inputClasses}
        {...props}
      />
      
      {error && (
        <p className="mt-2 text-sm text-red-600">{error}</p>
      )}
      
      {helper && !error && (
        <p className="mt-2 text-sm text-gray-500">{helper}</p>
      )}
    </div>
  )
})

Input.displayName = 'Input'

export default Input