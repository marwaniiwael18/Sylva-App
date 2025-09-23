// Mock data for the Sylva application

export const projects = [
  {
    id: 1,
    title: "Central Park Reforestation",
    description: "Join us in planting 500 new trees in Central Park to create a greener urban environment for all residents.",
    image: "https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=800&q=80",
    progress: 75,
    location: "Central Park, New York",
    coordinates: [-73.968285, 40.785091],
    targetTrees: 500,
    plantedTrees: 375,
    volunteers: 142,
    organizer: "NYC Parks Department",
    status: "active",
    startDate: "2024-01-15",
    endDate: "2024-06-15",
    category: "Reforestation",
    impact: {
      co2Saved: 2500,
      areaGreened: "5.2 acres"
    },
    comments: [
      {
        id: 1,
        user: "Alice Johnson",
        avatar: "https://ui-avatars.com/api/?name=Alice Johnson&background=22c55e&color=fff",
        comment: "Amazing initiative! Can't wait to participate this weekend.",
        date: "2024-01-20"
      },
      {
        id: 2,
        user: "Mike Chen",
        avatar: "https://ui-avatars.com/api/?name=Mike Chen&background=22c55e&color=fff",
        comment: "I've planted 15 trees so far. Great community spirit!",
        date: "2024-01-18"
      }
    ]
  },
  {
    id: 2,
    title: "Community Garden Initiative",
    description: "Transform unused urban lots into thriving community gardens where neighbors can grow fresh produce together.",
    image: "https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=800&q=80",
    progress: 45,
    location: "Downtown District",
    coordinates: [-73.985130, 40.748817],
    targetTrees: 0,
    plantedTrees: 0,
    volunteers: 89,
    organizer: "Green Communities Alliance",
    status: "active",
    startDate: "2024-02-01",
    endDate: "2024-08-01",
    category: "Urban Farming",
    impact: {
      co2Saved: 800,
      areaGreened: "2.1 acres"
    },
    comments: []
  },
  {
    id: 3,
    title: "Riverside Tree Planting",
    description: "Plant native species along the riverside to prevent erosion and create wildlife habitats.",
    image: "https://images.unsplash.com/photo-1574263867128-6c3b18ad0157?w=800&q=80",
    progress: 90,
    location: "Hudson River Park",
    coordinates: [-74.012794, 40.742054],
    targetTrees: 200,
    plantedTrees: 180,
    volunteers: 67,
    organizer: "River Conservation Society",
    status: "active",
    startDate: "2023-11-01",
    endDate: "2024-04-01",
    category: "Conservation",
    impact: {
      co2Saved: 1200,
      areaGreened: "1.8 acres"
    },
    comments: []
  }
]

export const events = [
  {
    id: 1,
    title: "Tree Planting Workshop",
    description: "Learn proper tree planting techniques from expert arborists and help plant 50 trees in Riverside Park.",
    image: "https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=800&q=80",
    date: "2024-02-15",
    time: "9:00 AM - 2:00 PM",
    location: "Riverside Park, Section 3",
    coordinates: [-73.972076, 40.787784],
    attendees: 45,
    maxAttendees: 60,
    organizer: "Urban Forestry Coalition",
    category: "Workshop",
    difficulty: "Beginner",
    equipment: "Provided",
    requirements: ["Comfortable clothes", "Water bottle", "Enthusiasm!"]
  },
  {
    id: 2,
    title: "Community Garden Harvest Festival",
    description: "Celebrate the harvest season with fresh produce from our community gardens. Food, music, and family fun!",
    image: "https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=800&q=80",
    date: "2024-02-22",
    time: "11:00 AM - 5:00 PM",
    location: "Community Garden Hub",
    coordinates: [-73.985130, 40.748817],
    attendees: 120,
    maxAttendees: 200,
    organizer: "Green Communities Alliance",
    category: "Festival",
    difficulty: "All levels",
    equipment: "Not needed",
    requirements: ["Bring your appetite!"]
  },
  {
    id: 3,
    title: "Urban Wildlife Habitat Creation",
    description: "Join conservation experts to create habitats for urban wildlife using native plants and sustainable practices.",
    image: "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&q=80",
    date: "2024-03-01",
    time: "10:00 AM - 3:00 PM",
    location: "Bryant Park",
    coordinates: [-73.983746, 40.753574],
    attendees: 28,
    maxAttendees: 40,
    organizer: "Wildlife Conservation Society",
    category: "Conservation",
    difficulty: "Intermediate",
    equipment: "Some provided",
    requirements: ["Gardening gloves", "Closed-toe shoes"]
  }
]

export const mapMarkers = [
  {
    id: 1,
    type: "project",
    title: "Central Park Reforestation",
    coordinates: [-73.968285, 40.785091],
    status: "active",
    progress: 75,
    description: "Active tree planting project with 375 trees planted so far."
  },
  {
    id: 2,
    type: "project",
    title: "Community Garden Initiative",
    coordinates: [-73.985130, 40.748817],
    status: "active",
    progress: 45,
    description: "Community garden project transforming urban lots."
  },
  {
    id: 3,
    type: "project",
    title: "Riverside Tree Planting",
    coordinates: [-74.012794, 40.742054],
    status: "active",
    progress: 90,
    description: "Native species planting along the riverside."
  },
  {
    id: 4,
    type: "report",
    title: "Dead Tree Removal Needed",
    coordinates: [-73.975207, 40.773325],
    status: "pending",
    reporter: "Jane Smith",
    description: "Large dead tree poses safety risk to pedestrians.",
    urgency: "high",
    reportDate: "2024-01-25"
  },
  {
    id: 5,
    type: "report",
    title: "Perfect Spot for New Trees",
    coordinates: [-73.971234, 40.767890],
    status: "pending",
    reporter: "Carlos Rodriguez",
    description: "Empty lot with good soil conditions for tree planting.",
    urgency: "low",
    reportDate: "2024-01-22"
  },
  {
    id: 6,
    type: "green-space",
    title: "Washington Square Park",
    coordinates: [-73.997332, 40.730823],
    status: "established",
    description: "Historic park with mature trees and gardens.",
    area: "9.75 acres",
    treeCount: 150
  },
  {
    id: 7,
    type: "green-space",
    title: "Madison Square Park",
    coordinates: [-73.988389, 40.742021],
    status: "established",
    description: "Urban oasis with diverse plant species and art installations.",
    area: "6.2 acres",
    treeCount: 95
  }
]

export const feedbackData = [
  {
    id: 1,
    user: "Sarah Johnson",
    avatar: "https://images.unsplash.com/photo-1494790108755-2616b612b5bb?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=150&q=80",
    rating: 5,
    type: "project",
    projectTitle: "Central Park Tree Restoration",
    comment: "This project was absolutely amazing! The organization was fantastic and I learned so much about urban forestry. The team leaders were knowledgeable and the whole experience was very rewarding.",
    date: "2 days ago",
    helpful: 12
  },
  {
    id: 2,
    user: "Michael Chen",
    avatar: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=150&q=80",
    rating: 4,
    type: "event",
    eventTitle: "Community Garden Workshop",
    comment: "Great workshop! Learned a lot about sustainable gardening practices. The only improvement would be to have more hands-on activities, but overall very educational and inspiring.",
    date: "5 days ago",
    helpful: 8
  },
  {
    id: 3,
    user: "Emily Rodriguez",
    avatar: "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=150&q=80",
    rating: 5,
    type: "project",
    projectTitle: "Brooklyn Rooftop Gardens",
    comment: "What an incredible initiative! I've been part of this project for 3 months now and it's been life-changing. The community we've built is amazing and seeing the gardens flourish is so rewarding.",
    date: "1 week ago",
    helpful: 15
  },
  {
    id: 4,
    user: "David Park",
    avatar: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=150&q=80",
    rating: 3,
    type: "event",
    eventTitle: "Earth Day Cleanup",
    comment: "The event was well-intentioned but felt a bit disorganized. We spent too much time waiting around and could have been more efficient with the cleanup process. The cause is great though!",
    date: "2 weeks ago",
    helpful: 5
  },
  {
    id: 5,
    user: "Lisa Thompson",
    avatar: "https://images.unsplash.com/photo-1517841905240-472988babdf9?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=150&q=80",
    rating: 5,
    type: "project",
    projectTitle: "Green Streets Initiative",
    comment: "Fantastic project! I love how it combines environmental action with community building. The app makes it easy to track progress and stay connected with other volunteers. Highly recommend!",
    date: "3 weeks ago",
    helpful: 20
  },
  {
    id: 6,
    user: "James Wilson",
    avatar: "https://images.unsplash.com/photo-1500648767791-00dcc994a43e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=150&q=80",
    rating: 4,
    type: "event",
    eventTitle: "Community Garden Workshop",
    comment: "Really enjoyed the workshop! The instructors were passionate and knowledgeable. I wish there was more follow-up material or additional sessions to continue learning.",
    date: "1 month ago",
    helpful: 7
  }
]

export const impactData = {
  totalTrees: 12750,
  totalCO2: 68500,
  totalArea: 145.7,
  activeProjects: 12,
  completedProjects: 38,
  totalVolunteers: 2856,
  monthlyGrowth: {
    trees: [
      { month: 'Jan', value: 450 },
      { month: 'Feb', value: 520 },
      { month: 'Mar', value: 680 },
      { month: 'Apr', value: 750 },
      { month: 'May', value: 890 },
      { month: 'Jun', value: 1020 }
    ],
    co2: [
      { month: 'Jan', value: 2250 },
      { month: 'Feb', value: 2600 },
      { month: 'Mar', value: 3400 },
      { month: 'Apr', value: 3750 },
      { month: 'May', value: 4450 },
      { month: 'Jun', value: 5100 }
    ],
    volunteers: [
      { month: 'Jan', value: 120 },
      { month: 'Feb', value: 145 },
      { month: 'Mar', value: 180 },
      { month: 'Apr', value: 210 },
      { month: 'May', value: 250 },
      { month: 'Jun', value: 290 }
    ]
  }
}

export const badges = [
  {
    id: 1,
    name: "Green Hero",
    description: "Planted 10+ trees",
    icon: "🌳",
    color: "bg-green-100 text-green-800",
    requirement: "Plant 10 trees"
  },
  {
    id: 2,
    name: "Tree Planter",
    description: "Participated in 5+ planting events",
    icon: "🌱",
    color: "bg-emerald-100 text-emerald-800",
    requirement: "Join 5 planting events"
  },
  {
    id: 3,
    name: "Community Builder",
    description: "Joined 3+ community projects",
    icon: "🤝",
    color: "bg-blue-100 text-blue-800",
    requirement: "Join 3 community projects"
  },
  {
    id: 4,
    name: "Eco Warrior",
    description: "Saved 100+ kg of CO₂",
    icon: "♻️",
    color: "bg-teal-100 text-teal-800",
    requirement: "Save 100kg of CO₂"
  },
  {
    id: 5,
    name: "New Member",
    description: "Welcome to Sylva!",
    icon: "✨",
    color: "bg-purple-100 text-purple-800",
    requirement: "Join the platform"
  }
]

export const recentActivities = [
  {
    id: 1,
    type: "tree_planted",
    message: "You planted 5 oak trees in Central Park",
    timestamp: "2 hours ago",
    icon: "🌳"
  },
  {
    id: 2,
    type: "event_joined",
    message: "Joined Tree Planting Workshop",
    timestamp: "1 day ago",
    icon: "📅"
  },
  {
    id: 3,
    type: "project_joined",
    message: "Joined Community Garden Initiative",
    timestamp: "3 days ago",
    icon: "🌱"
  },
  {
    id: 4,
    type: "badge_earned",
    message: "Earned Green Hero badge",
    timestamp: "5 days ago",
    icon: "🏆"
  },
  {
    id: 5,
    type: "report_submitted",
    message: "Submitted report for dead tree removal",
    timestamp: "1 week ago",
    icon: "📍"
  }
]