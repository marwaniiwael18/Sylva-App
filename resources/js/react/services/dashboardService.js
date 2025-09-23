/**
 * Dashboard Service
 * Handles dashboard-related API calls and statistics
 */

import api from './api'

export const dashboardService = {
  /**
   * Get dashboard statistics
   */
  async getStats() {
    try {
      const response = await api.get('/dashboard/stats')
      return response.data
    } catch (error) {
      throw new Error('Failed to fetch dashboard statistics')
    }
  },

  /**
   * Get user activity timeline
   */
  async getActivity(params = {}) {
    try {
      const response = await api.get('/dashboard/activity', { params })
      return response.data
    } catch (error) {
      throw new Error('Failed to fetch activity timeline')
    }
  },

  /**
   * Get impact metrics
   */
  async getImpactMetrics() {
    try {
      const response = await api.get('/dashboard/impact')
      return response.data
    } catch (error) {
      throw new Error('Failed to fetch impact metrics')
    }
  },

  /**
   * Get upcoming events for user
   */
  async getUpcomingEvents() {
    try {
      const response = await api.get('/dashboard/upcoming-events')
      return response.data
    } catch (error) {
      throw new Error('Failed to fetch upcoming events')
    }
  },

  /**
   * Get user's projects
   */
  async getUserProjects() {
    try {
      const response = await api.get('/dashboard/projects')
      return response.data
    } catch (error) {
      throw new Error('Failed to fetch user projects')
    }
  }
}