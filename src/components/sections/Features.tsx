import { Badge } from "@/components/ui/badge"
import { Card, CardContent } from "@/components/ui/card"
import { Dumbbell, Target, BarChart3, Apple, Settings } from "lucide-react"
export default function Features() {
    const features = [
    {
      icon: <Dumbbell className="h-8 w-8" />,
      title: "Smart Training Splits",
      description: "AI-powered workout plans with Push/Pull/Legs, Upper/Lower splits, and custom routines tailored to your goals."
    },
    {
      icon: <Target className="h-8 w-8" />,
      title: "Progress Tracking",
      description: "Track your lifts, monitor personal records, and visualize your fitness journey with detailed analytics."
    },
    {
      icon: <Apple className="h-8 w-8" />,
      title: "Nutrition Logging",
      description: "Log meals, track macros, and hit your daily calorie goals with our comprehensive nutrition system."
    },
    {
      icon: <BarChart3 className="h-8 w-8" />,
      title: "Advanced Analytics",
      description: "Detailed insights into your performance with weight tracking graphs and workout calendar views."
    }
  ];
    return (
        <section id="features" className="py-20">
        <div className="container mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <Badge variant="outline" className="mb-4"><Settings />Core Features</Badge>
            <h2 className="text-3xl md:text-5xl font-bold mb-4">Everything You Need to Succeed</h2>
            <p className="text-xl max-w-2xl mx-auto">
              Comprehensive tools designed to help you achieve your fitness goals faster and more efficiently.
            </p>
          </div>
          
          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            {features.map((feature, index) => (
              <Card key={index} className="text-center p-6 hover:shadow-lg transition-all duration-300 hover:scale-105 border-0 bg-card/50 backdrop-blur">
                <CardContent className="p-0">
                  <div className="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary/10 text-primary mb-4">
                    {feature.icon}
                  </div>
                  <h3 className="text-xl font-semibold mb-3">{feature.title}</h3>
                  <p>{feature.description}</p>
                </CardContent>
              </Card>
            ))}
          </div>
        </div>
      </section>
    )
}