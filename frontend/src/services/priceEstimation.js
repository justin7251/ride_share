// Constants for price calculation
const BASE_FARE = 5.00
const COST_PER_KM = 2.50
const COST_PER_MINUTE = 0.50
const SURGE_MULTIPLIER = 1.5 // For peak hours

export const calculatePrice = (distance, duration, rideType = 'economy') => {
  // Basic calculation
  let basePrice = BASE_FARE + (distance * COST_PER_KM) + (duration * COST_PER_MINUTE)

  // Apply ride type multiplier
  const multipliers = {
    economy: 1,
    comfort: 1.2,
    premium: 1.8
  }
  
  basePrice *= multipliers[rideType]

  // Check for surge pricing (simplified example)
  const currentHour = new Date().getHours()
  const isPeakHour = currentHour >= 7 && currentHour <= 9 || currentHour >= 17 && currentHour <= 19
  if (isPeakHour) {
    basePrice *= SURGE_MULTIPLIER
  }

  return {
    basePrice: basePrice.toFixed(2),
    isPeakHour,
    estimatedDuration: duration,
    estimatedDistance: distance,
    breakdown: {
      baseFare: BASE_FARE.toFixed(2),
      distanceCost: (distance * COST_PER_KM).toFixed(2),
      timeCost: (duration * COST_PER_MINUTE).toFixed(2),
      surgeMultiplier: isPeakHour ? SURGE_MULTIPLIER : 1
    }
  }
} 