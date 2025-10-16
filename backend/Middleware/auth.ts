import { Request, Response, NextFunction } from 'express';

export const requireAdmin = (req: Request, res: Response, next: NextFunction) => {
  const role = req.cookies.role;
  const token = req.cookies.token;
  
  if (!token || role !== 'ADMIN') {
    return res.status(403).json({ error: 'Access denied' });
  }
  
  next();
};