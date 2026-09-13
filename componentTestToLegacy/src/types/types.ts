export interface UserPayload {
  id: number
  name: string
  email: string
}

export interface OrderPayload {
  id: number
  product: string
  quantity: number
  price: number
  status: 'pending' | 'paid' | 'cancelled'
}

export interface AppPayload {
  user: UserPayload
  orders: OrderPayload[]
}