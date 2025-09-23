/**
 * Events Service
 * Handles all event-related API calls
 */

import api from './api'

export const eventsService = {
  /**
   * Get all events
   */
  async getAllEvents(params = {}) {
    try {
      const response = await api.get('/events', { params })
      return response.data
    } catch (error) {
      throw new Error('Failed to fetch events')
    }
  },

  /**
   * Get event by ID
   */
  async getEvent(id) {
    try {
      const response = await api.get(`/events/${id}`)
      return response.data
    } catch (error) {
      throw new Error('Failed to fetch event')
    }
  },

  /**
   * Create new event
   */
  async createEvent(eventData) {
    try {
      const response = await api.post('/events', eventData)
      return response.data
    } catch (error) {
      throw new Error(error.response?.data?.message || 'Failed to create event')
    }
  },

  /**
   * Update event
   */
  async updateEvent(id, eventData) {
    try {
      const response = await api.put(`/events/${id}`, eventData)
      return response.data
    } catch (error) {
      throw new Error(error.response?.data?.message || 'Failed to update event')
    }
  },

  /**
   * Delete event
   */
  async deleteEvent(id) {
    try {
      const response = await api.delete(`/events/${id}`)
      return response.data
    } catch (error) {
      throw new Error('Failed to delete event')
    }
  },

  /**
   * RSVP to event
   */
  async rsvpEvent(id) {
    try {
      const response = await api.post(`/events/${id}/rsvp`)
      return response.data
    } catch (error) {
      throw new Error('Failed to RSVP to event')
    }
  },

  /**
   * Cancel RSVP to event
   */
  async cancelRsvp(id) {
    try {
      const response = await api.delete(`/events/${id}/rsvp`)
      return response.data
    } catch (error) {
      throw new Error('Failed to cancel RSVP')
    }
  }
}