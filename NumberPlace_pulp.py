import pulp 
import numpy as np

init_array=np.array([[0,2,6],[0,3,1],[0,7,2],[1,0,0],[1,4,7],[1,6,5],[2,5,3],[2,8,4],[3,2,2],[3,3,8],\
    [3,7,1],[4,0,7],[4,8,3],[5,1,5],[5,5,4],[5,6,8],[6,0,3],[6,3,4],[7,2,1],[7,4,6],[7,8,0],[8,1,2],[8,5,0],[8,6,7]])
class Solution:
    def solve(self):
        for row in range(len(self)):
            for elem in range(3):
                self[row][elem] = self[row][elem] - 1

        # 変数
        x = pulp.LpVariable.dicts('x', ([0,1,2,3,4,5,6,7,8], [0,1,2,3,4,5,6,7,8],[0,1,2,3,4,5,6,7,8]), 0,1,pulp.LpInteger)

        #問題定義
        Problem = pulp.LpProblem(sense=pulp.LpMinimize)

        #目的関数
        Problem+=0

        #初期条件
        for i in self:
            [B0,B1,B2]=i
            for j in range(9):
                Problem+=x[B0][B1][B2]==1

        #各3次元行列の一列分に１が一個
        for i in range(9):
            for j in range(9):
                Problem+=pulp.lpSum([x[k][i][j] for k in range(9)])==1
                Problem+=pulp.lpSum([x[i][k][j] for k in range(9)])==1
                Problem+=pulp.lpSum([x[i][j][k] for k in range(9)])==1

        for i in range(3):
            for j in range(3):
                for k in range(9):
                    Problem+=pulp.lpSum([[x[i*3+l][j*3+n][k]] for l in range(3) for n in range(3)])==1

        results=Problem.solve()

        ans=np.array([])
        for i in range(9):
            ans_ax0=np.array([])
            for j in range(9):
                for k in range(9):
                    if x[i][j][k].value()==1:
                        if j==8:
                            print(k+1)
                            ans_ax0=np.append(ans_ax0,k+1)
                            ans=np.append(ans,ans_ax0)
                        else:
                            print(k+1,end="")
                            ans_ax0=np.append(ans_ax0,k+1)
        ans=ans.reshape(9,9)
        return ans

print(Solution.solve(init_array))