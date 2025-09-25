import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { ChevronRight, Star, Zap } from "lucide-react"
export default function Hero() {
    return (
         <section className="relative py-20 lg:py-32 overflow-hidden">
        <div className="absolute inset-0 bg-(--hero-gradient)"></div>
        <div className="container mx-auto px-4 sm:px-6 lg:px-8 relative">
          <div className="text-center max-w-4xl mx-auto">
            <Badge variant="outline" className="mb-6 px-4 py-2  border-secondary inline-flex items-center">
              <Zap className="h-4 w-4 mr-2" />
              AI-Powered Fitness Revolution
            </Badge>
            <h1 className="text-4xl md:text-6xl lg:text-7xl font-bold mb-6  bg-clip-text">
              Transform Your Fitness Journey with <span className="">GymGenius</span>
            </h1>
            <p className="text-xl md:text-2xl text-muted-foreground mb-8 max-w-2xl mx-auto">
              The ultimate AI health and fitness tracker that combines smart training splits, nutrition logging, and advanced progress tracking in one powerful platform.
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center items-center">
              <Button size="lg" className="text-lg px-8 py-6 shadow-(--shadow-fitness)">
                Start Your Journey
                <ChevronRight className="ml-2 h-5 w-5" />
              </Button>
              <Button variant="outline" size="lg" className="text-lg px-8 py-6">
                Watch Demo
              </Button>
            </div>
            <div className="flex items-center justify-center mt-8 space-x-2 text-sm text-muted-foreground">
              <div className="flex items-center">
                {Array.from({ length: 5 }).map((_, i) => (
                  <Star key={i} className="h-4 w-4 fill-primary text-primary" />
                ))}
              </div>
              <span>4.9/5 from 10,000+ users</span>
            </div>
          </div>
        </div>
      </section>
    )
}