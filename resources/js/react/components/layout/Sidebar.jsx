import React from 'react'
import { NavLink, useLocation } from 'react-router-dom'
import { motion } from 'framer-motion'
import { 
  LayoutDashboard, 
  Map, 
  TreePine, 
  Calendar, 
  MessageSquare, 
  BarChart3, 
  Menu,
  Leaf,
  Settings,
  HelpCircle
} from 'lucide-react'

const Sidebar = ({ collapsed, onToggle }) => {
  const location = useLocation()

  const navigation = [
    { name: 'Dashboard', href: '/dashboard', icon: LayoutDashboard },
    { name: 'Map', href: '/map', icon: Map },
    { name: 'Projects', href: '/projects', icon: TreePine },
    { name: 'Events', href: '/events', icon: Calendar },
    { name: 'Feedback', href: '/feedback', icon: MessageSquare },
    { name: 'Impact', href: '/impact', icon: BarChart3 },
  ]

  const bottomNavigation = [
    { name: 'Settings', href: '/settings', icon: Settings },
    { name: 'Help', href: '/help', icon: HelpCircle },
  ]

  return (
    <div className="flex flex-col h-full">
      {/* Logo */}
      <div className="flex items-center px-4 py-6 border-b border-gray-200">
        <motion.button
          onClick={onToggle}
          className="p-2 rounded-lg hover:bg-gray-100 transition-colors"
          whileHover={{ scale: 1.05 }}
          whileTap={{ scale: 0.95 }}
        >
          <Menu className="w-5 h-5 text-gray-600" />
        </motion.button>
        
        <motion.div 
          className="flex items-center gap-3 ml-3"
          initial={false}
          animate={{ 
            opacity: collapsed ? 0 : 1,
            width: collapsed ? 0 : 'auto'
          }}
          transition={{ duration: 0.2 }}
        >
          <div className="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
            <Leaf className="w-5 h-5 text-white" />
          </div>
          <span className="text-xl font-bold text-gray-900 whitespace-nowrap">Sylva</span>
        </motion.div>
      </div>

      {/* Navigation */}
      <nav className="flex-1 px-4 py-6 space-y-2">
        {navigation.map((item) => {
          const IconComponent = item.icon
          const isActive = location.pathname === item.href
          
          return (
            <NavLink
              key={item.name}
              to={item.href}
              className={({ isActive }) => `
                flex items-center px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                ${isActive 
                  ? 'bg-primary-50 text-primary-700 border border-primary-200' 
                  : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'
                }
              `}
            >
              <IconComponent className="w-5 h-5 flex-shrink-0" />
              <motion.span
                initial={false}
                animate={{ 
                  opacity: collapsed ? 0 : 1,
                  width: collapsed ? 0 : 'auto',
                  marginLeft: collapsed ? 0 : 12
                }}
                transition={{ duration: 0.2 }}
                className="whitespace-nowrap overflow-hidden"
              >
                {item.name}
              </motion.span>
              
              {isActive && (
                <motion.div
                  layoutId="activeTab"
                  className="absolute left-0 w-1 h-6 bg-primary-600 rounded-r-full"
                  initial={{ opacity: 0 }}
                  animate={{ opacity: 1 }}
                  transition={{ duration: 0.2 }}
                />
              )}
            </NavLink>
          )
        })}
      </nav>

      {/* Bottom Navigation */}
      <div className="px-4 py-6 border-t border-gray-200 space-y-2">
        {bottomNavigation.map((item) => {
          const IconComponent = item.icon
          
          return (
            <NavLink
              key={item.name}
              to={item.href}
              className="flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-all duration-200"
            >
              <IconComponent className="w-5 h-5 flex-shrink-0" />
              <motion.span
                initial={false}
                animate={{ 
                  opacity: collapsed ? 0 : 1,
                  width: collapsed ? 0 : 'auto',
                  marginLeft: collapsed ? 0 : 12
                }}
                transition={{ duration: 0.2 }}
                className="whitespace-nowrap overflow-hidden"
              >
                {item.name}
              </motion.span>
            </NavLink>
          )
        })}
      </div>
    </div>
  )
}

export default Sidebar