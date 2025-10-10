import { PrismaClient } from "@prisma/client"; 
import bcrypt from "bcrypt";
import crypto from "crypto";
import jwt from "jsonwebtoken";
const JWT_SECRET = process.env.JWT ?? "secret";



const prisma = new PrismaClient();
import { Request, Response } from "express";
export const registerUser = async (req: Request, res: Response): Promise<void> => {
    const { userName, password, email } = req.body;

    if (!userName || !password || !email) {
        res.status(400).json({ message: 'All fields are required' });
    }

    try {
        const existingUser = await prisma.user.findFirst({
            where: {
                OR: [
                    { userName },
                    { email }
                ]
            }
        });

        if (existingUser) {
            res.status(409).json({ message: 'Username or email already exists' });
        }
        
        const hashedPassword = await bcrypt.hash(password, 10);
        //const verificationToken = crypto.randomBytes(32).toString('hex');
        const newUser = await prisma.user.create({
            data: {
                userName,
                password: hashedPassword,
                email,
                isVerified: false,
                role: 'USER'
                //verificationToken
            }
        });

        // Send verification email logic here (omitted for brevity)

        res.status(201).json({ message: 'User registered successfully. Please check your email to verify your account.' });
        return;
    } catch (error) {
        console.error(error);
        res.status(500).json({ message: 'Internal server error' });
    }
}


export const loginUser= async (req: Request, res: Response): Promise<any> => {
    const { email, password } = req.body;

    try {
        const user = await prisma.user.findUnique({
            where: { email }
        });
3
        if (!user) {
            return res.status(401).json({ message: "Invalid email or password" });
        }

        const isPasswordValid = await bcrypt.compare(password, user.password);
        if (!isPasswordValid) {
            res.status(401).json({ message: "Invalid email or password" });
        }

        const accessToken = jwt.sign({ id: user?.id, username: user.userName, role: user.role }, JWT_SECRET, { expiresIn: "1h" });


        res.status(200).json({
            message: "Login successful",
            accessToken,
            user: {
                id: user?.id,
                username: user?.userName,
                role: user.role,
                email: user?.email
            }
        });
    } catch (error: any) {
        console.error("Error during login:", error);
        res.status(500).json({ message: "Internal server error" });
    }
};


export const logoutUser = async (req: Request, res: Response): Promise<void> => {
    const { token } = req.body;
    res.clearCookie("refreshToken");
    res.status(200).json({ message: "Logout successful" });
}

