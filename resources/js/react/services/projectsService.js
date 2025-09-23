/**
 * Projects Service
 * Handles all project-related API calls
 */

import api from './api'

export const projectsService = {
  /**
   * Get all projects
   */
  async getAllProjects(params = {}) {
    try {
      const response = await api.get('/projects', { params })
      return response.data
    } catch (error) {
      throw new Error('Failed to fetch projects')
    }
  },

  /**
   * Get project by ID
   */
  async getProject(id) {
    try {
      const response = await api.get(`/projects/${id}`)
      return response.data
    } catch (error) {
      throw new Error('Failed to fetch project')
    }
  },

  /**
   * Create new project
   */
  async createProject(projectData) {
    try {
      const response = await api.post('/projects', projectData)
      return response.data
    } catch (error) {
      throw new Error(error.response?.data?.message || 'Failed to create project')
    }
  },

  /**
   * Update project
   */
  async updateProject(id, projectData) {
    try {
      const response = await api.put(`/projects/${id}`, projectData)
      return response.data
    } catch (error) {
      throw new Error(error.response?.data?.message || 'Failed to update project')
    }
  },

  /**
   * Delete project
   */
  async deleteProject(id) {
    try {
      const response = await api.delete(`/projects/${id}`)
      return response.data
    } catch (error) {
      throw new Error('Failed to delete project')
    }
  },

  /**
   * Join project
   */
  async joinProject(id) {
    try {
      const response = await api.post(`/projects/${id}/join`)
      return response.data
    } catch (error) {
      throw new Error('Failed to join project')
    }
  },

  /**
   * Leave project
   */
  async leaveProject(id) {
    try {
      const response = await api.post(`/projects/${id}/leave`)
      return response.data
    } catch (error) {
      throw new Error('Failed to leave project')
    }
  }
}